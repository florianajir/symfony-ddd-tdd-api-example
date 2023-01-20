<?php

namespace App\Shared\Application\Controller;

use App\Shared\Infrastructure\Formatter\ConstraintViolationFormatter;
use App\Shared\Application\Message\Registration;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    #[OA\Tag('user', 'Register a user')]
    #[OA\Response(response: 202, description: 'Registration is processing')]
    #[OA\Response(response: 400, description: 'Invalid registration data')]
    #[OA\RequestBody(
        description: 'Registration data',
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Registration::class)
        )
    )]
    #[Route('/registration', name: 'registration', methods: ['POST'], format: 'json')]
    #[Security]
    public function register(
        MessageBusInterface $bus,
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ): JsonResponse {
        $registration = $serializer->deserialize(
            $request->getContent(),
            Registration::class,
            JsonEncoder::FORMAT
        );
        $errors = $validator->validate($registration);
        if (count($errors) > 0) {
            $errorArray = ConstraintViolationFormatter::formatToArray($errors);
            $logger->warning('Registration failed', $errorArray);

            return new JsonResponse($errorArray, 400);
        }
        $bus->dispatch($registration);

        return new JsonResponse(status: 202);
    }
}
