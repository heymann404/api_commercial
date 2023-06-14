<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testAuth(): void
    {
        $client = static::createClient([], [
            'Content-Type' => 'application/json'
        ]);

        $client->request(
            'POST',
            '/api/login_check',
            [], [], [],
            '{"username": "commercial1@email.com", "password": "123"}'
        );

        $this->assertResponseIsSuccessful();
        $this->assertStringStartsWith('{"token":', $client->getResponse()->getContent());
    }
}
