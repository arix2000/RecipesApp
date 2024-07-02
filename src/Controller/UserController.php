<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\RecipeRepository;
use App\Services\PagingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private PagingService $service;

    public function __construct(PagingService $service)
    {
        $this->service = $service;
    }

    #[Route('/user/recipes', name: 'user_recipes')]
    public function yourRecipes(SessionInterface $session, Request $request, PaginatorInterface $paginator): Response
    {
        $session->set('backRoute', 'user_recipes');

        /** @var User $user */
        $user = $this->getUser();

        $pagination = $this->service->getUserRecipesPagination($user->getId(), $request, $paginator)->getPagination();

        if ($request->isXmlHttpRequest()) {
            return $this->render('recipe/recipes_list.html.twig', ['pagination' => $pagination]);
        }

        return $this->render('/user/your_recipes.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/authentication/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'current_route' => 'app_login'
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security                    $security,
        EntityManagerInterface      $entityManager
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('user/authentication/register.html.twig', [
            'registrationForm' => $form,
            'current_route' => 'app_register',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout()
    {

    }
}
