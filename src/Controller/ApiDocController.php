<?php

namespace App\Controller;

use App\Services\DocsParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiDocController extends AbstractController
{
    #[Route('/api/doc', name: 'app_api_doc')]
    public function index(DocsParser $parser, string $projectDir): Response
    {
        $filePath = $projectDir . '/var/data/docs.json';
        $endpoints = $parser->parse($filePath);

        return $this->render('api_doc/index.html.twig', [
            'endpoints' => $endpoints
        ]);
    }
}
