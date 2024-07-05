<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\ApiFormatter;
use App\Services\PagingService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserController extends AbstractController
{
    private PagingService $pagingService;
    private ApiFormatter $apiFormatter;
    private UserRepository $repository;

    public function __construct(
        PagingService  $pagingService,
        ApiFormatter   $apiFormatter,
        UserRepository $repository
    )
    {
        $this->pagingService = $pagingService;
        $this->apiFormatter = $apiFormatter;
        $this->repository = $repository;
    }

    #[Route('/api/user/recipes/{id}', name: 'api_user_recipes', methods: ['GET'])]
    public function apiUserRecipes($id, Request $request, PaginatorInterface $paginator): Response
    {
        $isUserNotExist = $this->repository->findOneBy(['id' => $id]) === null;
        if ($isUserNotExist)
            return $this->apiFormatter->formatError("User not exists!");
        $recipesPagination = $this->pagingService->getUserRecipesPagination($id, $request, $paginator);
        $recipes = $recipesPagination->getRecipes();
        $pagination = $recipesPagination->getPagination();

        return $this->apiFormatter->formatResponse(
            $this->pagingService->getPaginatedResponse($pagination, $recipes));
    }

    #[Route('api/user/login', name: 'api_user_login', methods: ['POST'])]
    public function apiLogin(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->getInvalidJsonDataResponse();
        }

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findByEmail($email);
        if (!$user) {
            return $this->apiFormatter->formatError('User not found', Response::HTTP_NOT_FOUND);
        }
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->apiFormatter->formatError('Invalid password');
        }

        return $this->apiFormatter->formatResponse(User::getMap($user));
    }

    #[Route('api/user/register', name: 'api_user_register', methods: ['POST'])]
    public function apiRegister(Request                     $request,
                                ValidatorInterface          $validator,
                                UserPasswordHasherInterface $passwordHasher,): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->getInvalidJsonDataResponse();
        }

        $user = new User();
        $user->setFirstName($data['firstName'] ?? null);
        $user->setLastName($data['lastName'] ?? null);
        $user->setEmail($data['email'] ?? null);
        $user->setPassword($passwordHasher->hashPassword(
            $user,
            $data['password'] ?? null
        ));

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->apiFormatter->formatError($errors[0]->getMessage());
        }

        $this->repository->add($user);

        return $this->apiFormatter->formatResponse(['result' => 'OK']);
    }

    private function getInvalidJsonDataResponse(): JsonResponse
    {
        return $this->apiFormatter->formatError("Invalid JSON data.");
    }
}
