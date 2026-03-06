<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\MapQueryString;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use App\Dto\PetStatusQuery;
use App\Exception\StatusCodeException;
use App\Model\Pet\Pet;
use App\Model\Pet\Enum\Status;
use App\Service\PetApiService;

final class PetsController extends AbstractController
{
    public function __construct(
        private PetApiService $apiService, 
    ) {
    }

    #[Route('/pets', name: 'app_pets', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PetsController.php',
        ]);
    }

    #[Route('/pets', name: 'add_pet', methods: ['POST'])]
    public function addPet(): JsonResponse
    {
        $pet = new Pet();
        $form = $this->createForm(PetType::class, $pet);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->json($pet);
        }

        return $this->render('pet/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pets/findByStatus', name: 'find_by_status', methods: ['GET'])]
    public function findByStatus(
        Request $request,
        SerializerInterface $serializer, 
        ValidatorInterface $validator
    ): JsonResponse
    {
        try {
            $query = $request->query->all();
            if (array_key_exists('status', $query) && !is_array($query['status'])) {
                $query['status'] = ($query['status'] === "") ? [] : [$query['status']];
            }
            
            $queryDto = $serializer->denormalize($query, PetStatusQuery::class);
            $this->validateErrors($validator->validate($queryDto));

            $pets = $this->apiService->getPetsByStatuses($queryDto->status);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }

        return $this->json($pets);
    }

    #[Route('/pets/{id}', name: 'find_by_id', methods: ['GET'])]
    public function findById(int $id): JsonResponse
    {
        try {
            $pet = $this->apiService->getPetById($id);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        } catch (\Exception $e) { 
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }

        return $this->json($pet);
    }

    private function validateErrors(ConstraintViolationList $errors)
    {
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            throw new StatusCodeException(implode(', ', $errorMessages), 400);
        }
    }
}
