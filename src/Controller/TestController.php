<?php

namespace App\Controller;

use App\Entity\Test;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    #[Route('/')]
    public function index()
    {
        return new JsonResponse(['message' => 'Hello!']);
    }

    #[Route('/hello/{name}', name: 'api_hello', methods: 'GET')]
    public function hello($name)
    {
        return new JsonResponse(['message' => 'Hello ' . $name]);
    }
}
