<?php

namespace App\Shared\Application\Client;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class OpenFoodFactsFr
{
    private HttpClientInterface $client;

    private const FIELDS = [
        'product_name',
        'code',
        'brands',
        'ingredients_text',
        'allergens',
        'nutriscore_score',
        'nutrition_grades',
    ];

    public function __construct(HttpClientInterface $offFrClient)
    {
        $this->client = $offFrClient;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function request($method, $endpoint, $query = []): ResponseInterface
    {
        return $this->client->request(
            $method,
            $endpoint,
            [
                'query' => array_merge([
                    'fields' => self::getFieldsCS(),
                    'json' => true,
                ], $query),
            ]
        );
    }

    public static function getFieldsCS(): string
    {
        return implode(',', self::FIELDS);
    }

}
