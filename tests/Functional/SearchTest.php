<?php

namespace App\Tests\Functional;

class SearchTest extends AuthenticatedTestCase
{
    public function testSearchWithoutParams()
    {
        $this->authenticateWithJwt();
        $this->client->request('GET', '/api/search');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testSearch()
    {
        $this->authenticateWithJwt();
        $this->client->request('GET', '/api/search?name=water');

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $data = json_decode($content, true);
        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('products', $data);
        $this->assertNotEmpty($data['products']);
    }
}
