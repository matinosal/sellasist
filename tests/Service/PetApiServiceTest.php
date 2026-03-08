<?php

namespace App\Tests\Service;

use App\Service\PetApiService;
use App\Model\Pet\Pet;
use App\Exception\StatusCodeException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class PetApiServiceTest extends TestCase
{
    private $client;
    private $serializer;
    private PetApiService $service;

    protected function setUp(): void
    {
        $this->client = $this->createMock(HttpClientInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->service = new PetApiService($this->client, $this->serializer);
    }

    public function testGetPetByIdReturnsPet(): void
    {
        $pet = new Pet();
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContent')->willReturn('{"id":1,"name":"Carmelo"}');

        $this->client->expects($this->once())
            ->method('request')
            ->with('GET', 'https://petstore.swagger.io/v2/pet/1')
            ->willReturn($response);

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('{"id":1,"name":"Carmelo"}', Pet::class, 'json')
            ->willReturn($pet);

        $result = $this->service->getPetById(1);
        $this->assertSame($pet, $result);
    }

    public function testGetPetByIdThrowsExceptionOn404(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(404);

        $this->client->method('request')->willReturn($response);

        $this->expectException(StatusCodeException::class);
        $this->expectExceptionMessage('Pet not found');

        $this->service->getPetById(999);
    }

    public function testGetPetsByStatusesReturnsArray(): void
    {
        $pet = new Pet();
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getContent')->willReturn('[{"id":1,"name":"Carmelo","status":"available"}]');

        $this->client->method('request')->willReturn($response);
        $this->serializer->method('deserialize')->willReturn([$pet]);

        $result = $this->service->getPetsByStatuses(['available']);
        $this->assertIsArray($result);
        $this->assertSame([$pet], $result);
    }

    public function testAddPetReturnsPet(): void
    {
        $pet = new Pet();
        $this->serializer->method('serialize')->willReturn('{"id":1,"name":"Doggie"}');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->method('request')->willReturn($response);

        $result = $this->service->addPet($pet);
        $this->assertSame($pet, $result);
    }

    public function testEditPetReturnsPet(): void
    {
        $pet = new Pet();
        $this->serializer->method('serialize')->willReturn('{"id":1,"name":"Doggie"}');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->method('request')->willReturn($response);

        $result = $this->service->editPet($pet);
        $this->assertSame($pet, $result);
    }

    public function testDeletePetById(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->method('request')->willReturn($response);

        $this->service->deletePetById(1);
        $this->assertTrue(true);
    }

    public function testUpdatePetById(): void
    {
        $pet = new Pet();
        $this->serializer->method('serialize')->willReturn('{"id":1,"name":"Doggie"}');
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);

        $this->client->method('request')->willReturn($response);

        $this->service->updatePetById($pet, 1);
        $this->assertTrue(true);
    }
}