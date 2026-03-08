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
use Symfony\Component\Serializer\Annotation\MapRequestPayload;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use OpenApi\Attributes as OA;
use App\Dto\PetDto;
use App\Dto\PetStatusQueryDto;
use App\Exception\StatusCodeException;
use App\Form\PetType;
use App\Model\Pet\Pet;
use App\Model\Pet\Enum\Status;
use App\Service\PetApiService;
use App\Requset\ValueResolver\GetQueryStringValueResolver;
use App\Requset\ValueResolver\MapRequestPayloadResolver;

final class PetsController extends AbstractController
{
    public function __construct(
        private PetApiService $apiService, 
    ) {
    }

    #[Route('/pet', name: 'add_pet', methods: ['POST'])]
    public function addPet(
        #[MapRequestPayload(PayloadRequestValueResolver::class, source: "payload")]
        Pet $pet
    ): JsonResponse
    {
        try {
            $this->apiService->addPet($pet);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }  catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }
         
        return $this->json($pet, 201);
    }

    #[Route('/pet', name: 'edit_pet', methods: ['PUT'])]
    public function editPet(
        #[MapRequestPayload(PayloadRequestValueResolver::class, source: "payload")]
        Pet $pet
    ): JsonResponse
    {
        try {
            $this->apiService->editPet($pet);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }  catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }
         
        return $this->json($pet, 201);
    }

    #[Route('/pet/findByStatus', name: 'find_by_status', methods: ['GET'])]
    public function findByStatus(
        #[MapQueryString(QueryStringValueResolver::class)]
        PetStatusQueryDto $queryDto
    ): JsonResponse
    {
        try {
            $pets = $this->apiService->getPetsByStatuses($queryDto->status);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }

        return $this->json($pets);
    }

    #[Route('/pet/{id}', name: 'delete_pet', methods: ['DELETE'])]
    public function deletePet(int $id): JsonResponse
    {
        try {
            $this->apiService->deletePetById($id);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        } catch (\Exception $e) { 
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }
         
        return $this->json(['success' => true], 200);
    }

    #[Route('/pet/{id}', name: 'find_by_id', methods: ['GET'])]
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

    #[Route('/pet/{id}', name: 'update_by_id', methods: ['POST'])]
    public function updatePetById(
        int $id,
        #[MapRequestPayload(PayloadRequestValueResolver::class, source: "payload")]
        Pet $pet
    ): JsonResponse
    {
        try {
            $pet = $this->apiService->updatePetById($pet, $id);
        } catch (StatusCodeException $e) {
            return $this->json(['error' => $e->getMessage()], $e->getCode() ?: 500);
        } catch (\Exception $e) { 
            return $this->json(['error' => 'An unexpected error occurred' . $e->getMessage()], 500);
        }

        return $this->json(['success' => true], 200);
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
