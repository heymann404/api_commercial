<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;

class NoteDeFraisControllerTest extends WebTestCase
{
    public function testNew(): void
    {
        $client = static::createClient([], [
            'Content-Type' => 'application/json'
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('commercial1@email.com');

        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/noteDeFrais',
            [], [], [],
            '{"codeTypeDeNote": "ESSENCE","societe": 1,"montant": 450.20,"dateDeLaNote": "2022-12-10"}'
        );

        $response = $client->getResponse()->getContent();

        // code de retour 200
        $this->assertResponseIsSuccessful();

        // la réponse contient une note de frais
        $this->assertArrayHasKey('dateDeLaNote', json_decode($response, true));
    }

    public function testShow(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('commercial1@email.com');

        $client->loginUser($testUser);

        $client->request(
            'GET',
            '/api/noteDeFrais/1'
        );

        $response = $client->getResponse()->getContent();

        // code de retour 200
        $this->assertResponseIsSuccessful();

        // la réponse contient une note de frais
        $this->assertArrayHasKey('dateDeLaNote', json_decode($response, true));
    }

    public function testList(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('commercial1@email.com');

        $client->loginUser($testUser);

        $client->request(
            'GET',
            '/api/noteDeFrais'
        );

        $response = $client->getResponse()->getContent();

        // code de retour 200
        $this->assertResponseIsSuccessful();

        // la réponse contient un tableau d'au moins un élément
        $this->assertArrayHasKey(0, json_decode($response, true));
    }

    public function testEdit(): void
    {
        $client = static::createClient([], [
            'Content-Type' => 'application/json'
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('commercial1@email.com');

        $client->loginUser($testUser);

        $client->request(
            'PUT',
            '/api/noteDeFrais/1',
            [], [], [],
            '{"codeTypeDeNote": "ESSENCE","societe": 1,"montant": 300.20,"dateDeLaNote": "2022-12-10"}'
        );

        $response = $client->getResponse()->getContent();

        // code de retour 200
        $this->assertResponseIsSuccessful();

        // la réponse contient une note de frais
        $this->assertArrayHasKey('dateDeLaNote', json_decode($response, true));
    }

    public function testDelete(): void
    {
        $client = static::createClient([], [
            'Content-Type' => 'application/json',
        ]);
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('commercial1@email.com');

        $client->loginUser($testUser);

        $client->request(
            'DELETE',
            '/api/noteDeFrais/1'
        );

        $response = $client->getResponse()->getContent();

        // code de retour 200
        $this->assertResponseIsSuccessful();

        // la réponse contient un message de confirmation
        $this->assertStringStartsWith("La note de frais", trim($response, '"'));
    }

}
