<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class СтатусКонтроллер extends AbstractController
{
    #[Route('/api', name: 'статус_сервера', methods: ['GET'])]
    public function статусСервера(Request $request): JsonResponse
    {
        return new JsonResponse([
            'статус' => 'сервер работает',
            'хост' => $request->getHost(),
            'протокол' => $request->getScheme(),
        ]);
    }

    #[Route('/api/ping', name: 'проверка_ping', methods: ['GET'])]
    public function проверкаPing(): JsonResponse
    {
        return new JsonResponse([
            'статус' => 'понг',
        ]);
    }
}
