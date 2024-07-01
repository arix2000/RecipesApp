<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiRecipeController extends AbstractController
{
    #[Route('/api/recipes', name: 'api_recipe')]
    public function recipes(): Response
    {
        return new Response();
    }
}
