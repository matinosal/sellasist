<?php

namespace App\Service;

use App\Model\Pet\Pet;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Exception\StatusCodeException;

class PetApiService
{
    const API_URL = 'https://petstore.swagger.io/v2/pet/';

    public function __construct(
        private HttpClientInterface $client,
        private SerializerInterface $serializer
    ) {
    }

    public function getPetById(int $id): Pet
    {
        $response = $this->client->request('GET', self::API_URL . $id);
        $this->checkStatusCode($response->getStatusCode());

        $jsonContent = $response->getContent();
        return $this->serializer->deserialize($jsonContent, Pet::class, 'json');
    }

    public function getPetsByStatuses(array $status): array
    {
        $response = $this->client->request('GET', self::API_URL . 'findByStatus', [
            'query' => ['status' => implode(',', $status)]
        ]);
        $this->checkStatusCode($response->getStatusCode());

        $jsonContent = $response->getContent();
        return $this->serializer->deserialize($jsonContent, Pet::class . '[]', 'json');
    }

    public function addPet(Pet $pet): Pet
    {
        $jsonContent = $this->serializer->serialize($pet, 'json');
        $response = $this->client->request('POST', self::API_URL, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $jsonContent
        ]);
        $this->checkStatusCode($response->getStatusCode());

        return $pet;
    }

    public function editPet(Pet $pet): Pet
    {
        $jsonContent = $this->serializer->serialize($pet, 'json');
        $response = $this->client->request('PUT', self::API_URL, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $jsonContent
        ]);
        $this->checkStatusCode($response->getStatusCode());

        return $pet;
    }

    
    public function deletePetById(int $id): void
    {
        $response = $this->client->request('DELETE', self::API_URL . $id);
        $this->checkStatusCode($response->getStatusCode());
    }

    public function updatePetById(Pet $pet, int $id): void
    {
        $json = $this->serializer->serialize($pet, 'json');
        $content = json_decode($json, true);
        $response = $this->client->request('POST', self::API_URL . $id, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $content
        ]);
        $this->checkStatusCode($response->getStatusCode());
    }
    
    private function checkStatusCode(int $statusCode): void
    {
        if ($statusCode === 404) {
            throw new StatusCodeException('Pet not found', 404);
        } else if ($statusCode === 400) {
            throw new StatusCodeException('Invalid ID supplied', 400);
        } else if ($statusCode !== 200) {
            throw new StatusCodeException('Unexpected error occurred', 500);
        }
    }
}