<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testGuest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
//        $this->assertSame('http://localhost:8088/login', $client->getResponse()->headers->get('Location'));
    }

    public function testSuccess(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin@mail.com',
            'PHP_AUTH_PW' => 'AdminAdmin',
        ]);
        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Hello', $crawler->filter('h1')->text());
    }

}