<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation\MapQueryString;
use Symfony\Component\Serializer\Annotation\MapRequestPayload;
use Symfony\Component\Validator\ConstraintViolationList;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use App\Dto\PetStatusQueryDto;
use App\Exception\StatusCodeException;
use App\Form\PetType;
use App\Model\Pet\Pet;
use App\Model\Pet\Enum\Status;
use App\Service\PetApiService;
use App\Requset\ValueResolver\GetQueryStringValueResolver;
use App\Requset\ValueResolver\MapRequestPayloadResolver;

#[OA\Tag(name: 'pets')]
final class PetsController extends AbstractController
{
    public function __construct(
        private PetApiService $apiService, 
    ) {
    }

    #[Route('/pet', name: 'add_pet', methods: ['POST'])]
    #[OA\Post(path: "/pet", summary: "Add a new pet")]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Pet::class))
    )]
    #[OA\Response(
        response: 201,
        description: "Pet created",
        content: new OA\JsonContent(ref: new Model(type: Pet::class))
    )]
    #[OA\Response(response: 405, description: "Invalid input")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
    #[OA\Put(path: "/pet",summary: "Update existing pet")]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Pet::class))
    )]
    #[OA\Response(response: 200, description: "Pet edited", content: new OA\JsonContent(ref: new Model(type: Pet::class)))]
    #[OA\Response(response: 400, description: "Invalid ID supplied")]
    #[OA\Response(response: 404, description: "Pet not found")]
    #[OA\Response(response: 405, description: "Invalid input")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
         
        return $this->json($pet, 200);
    }

    #[Route('/pet/findByStatus', name: 'find_by_status', methods: ['GET'])]
    #[OA\Get(
        path: "/pet/findByStatus",
        summary: "Find pet by status",
        parameters: [
            new OA\Parameter(
                name: "status[]",
                in: "query",
                required: true,
                description: "Filter pets by status",
                schema: new OA\Schema(
                    type: "array",
                    items: new OA\Items(
                        ref: "#/components/schemas/Status"
                    )
                ),
                style: "form",
                explode: true
            )
        ]
    )]
    #[OA\Response(response: 200, description: "List of pets",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                ref: new Model(type: Pet::class)
            )
        )
    )]
    #[OA\Response(response: 400, description: "Invalid ID supplied")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
    #[OA\Delete( path: "/pet/{id}", summary: "Delete pet")]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID of pet to delete", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Pet deleted", 
        content: new OA\JsonContent(
            type: "object", 
            properties: ['success' => new OA\Property(property: "success", type: 'boolean', example: true)])
    )]
    #[OA\Response(response: 400, description: "Invalid ID supplied")]
    #[OA\Response(response: 404, description: "Pet not found")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
    #[OA\Get(path: "/pet/{id}", summary: "Find pet by ID")]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID of pet to return", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Pet found", 
        content: new OA\JsonContent(ref: new Model(type: Pet::class))
    )]
    #[OA\Response(response: 400, description: "Invalid ID supplied")]
    #[OA\Response(response: 404, description: "Pet not found")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
    #[OA\Post(path: "/pet/{id}", summary: "Update pet by ID")]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Pet::class)
        )
    )]
    #[OA\Parameter(name: "id", in: "path", required: true, description: "ID of pet to update", schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Pet updated", 
        content: new OA\JsonContent(
            type: "object", 
            properties: ['success' => new OA\Property(property: "success", type: 'boolean', example: true)])
    )]
    #[OA\Response(response: 400, description: "Invalid ID supplied")]
    #[OA\Response(response: 500, description: "An unexpected error occurred")]
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
