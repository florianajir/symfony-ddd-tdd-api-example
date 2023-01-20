<?php

namespace App\Search\Controller;

use App\Shared\Application\Client\OpenFoodFactsFr;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SearchController extends AbstractController
{
    /**
     * Search for a product in OpenFoodFactsApi
     *
     * This call takes into account all criteria defined in query and exclude user product exclusion list if there is any
     */
    #[OA\Tag('search', 'Search for product by criteria')]
    #[OA\Response(response: 200, description: 'Search successful')]
    #[OA\Response(response: 400, description: 'Invalid request')]
    #[OA\QueryParameter(
        name: 'name',
        description: 'Product name',
        required: false,
    )]
    #[Route('/search', name: 'search', methods: ['GET'], format: 'json')]
    #[Security(name: 'Bearer')]
    public function search(
        Request $request,
        OpenFoodFactsFr $client,
        LoggerInterface $logger
    ): JsonResponse {
        if (empty($request->query->all())) {
            $logger->debug('Accessing search endpoint without setting any parameter');

            return new JsonResponse(['error' => 'At least one parameter required'], 400);
        }
        try {
            $response = $client->request(
                'GET',
                '/api/v2/search',
                [
//                    'action' => 'process',
//                    'tagtype_0' => 'label',
//                    'tag_0' => $request->query->get('name'),
//                    'tag_contains_0' => 'contains',
                ]
            );
        } catch (TransportExceptionInterface $e) {
            $logger->error('Error requesting third party API');

            return new JsonResponse($e->getMessage(), status: 500);
        }

        return new JsonResponse($response->getContent(), status: 200, json: true);
    }
}
