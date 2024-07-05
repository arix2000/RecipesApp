<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeFormType;
use App\Model\UiRecipe;
use App\Repository\RecipeRepository;
use App\Services\PagingService;
use App\Services\RecipeService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Translation\LocaleSwitcher;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private EntityManagerInterface $entityManager;
    private RecipeService $recipeService;
    private PagingService $pagingService;

    public function __construct(
        RecipeRepository       $recipeRepository,
        EntityManagerInterface $entityManager,
        RequestStack           $requestStack,
        string                 $projectDir,
        PagingService          $pagingService,
    )
    {
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $request = $requestStack->getCurrentRequest();
        $hostUrl = $request->getSchemeAndHttpHost();
        $this->recipeService = new RecipeService($projectDir, $hostUrl);
        $this->pagingService = $pagingService;
    }

    #[Route('/', name: 'recipes')]
    public function recipes(Request $request, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        $session->set('backRoute', 'recipes');
        $pagination = $this->pagingService->getRecipePagination($request, $paginator)->getPagination();
        if ($request->isXmlHttpRequest()) {
            return $this->render('recipe/recipes_list.html.twig', ['pagination' => $pagination]);
        }

        return $this->render('recipe/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(Request $request, PaginatorInterface $paginator, SessionInterface $session): Response
    {
        $session->set('backRoute', 'search');
        $recipePagination = $this->pagingService->getRecipePagination($request, $paginator, true);
        $pagination = $recipePagination->getPagination();
        $searchTerm = $recipePagination->getSearchTerm();

        if ($request->isXmlHttpRequest()) {
            return $this->render('recipe/recipes_list.html.twig', ['pagination' => $pagination]);
        }
        return $this->render('recipe/search/index.html.twig', [
            'pagination' => $pagination,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/recipe/create', name: 'create_recipe')]
    public function createRecipe(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRecipe = $form->getData();
            $newRecipe = $this->recipeService->getUpdatedRecipe($newRecipe, $form, $user);
            $this->entityManager->persist($newRecipe);
            $this->entityManager->flush();
            return $this->redirectToRoute('user_recipes');
        }
        return $this->render("recipe/details/create.html.twig", ["form" => $form->createView()]);
    }

    #[Route('/recipe/edit/{id}', name: 'edit_recipe')]
    public function editRecipe($id, Request $request, SessionInterface $session): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $recipe = $this->recipeRepository->find($id);
        $recipe->setIngredients(implode(PHP_EOL, json_decode($recipe->getIngredients())));
        $recipe->setDirections(implode(PHP_EOL, json_decode($recipe->getDirections())));
        $recipe->setNer(implode(PHP_EOL, json_decode($recipe->getNer())));
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $this->recipeService->getUpdatedRecipe($recipe, $form, $user);
            $this->entityManager->flush();
            $session->set("isFromEditPage", true);
            return $this->redirectToRoute('recipe', ["id" => $id]);
        }


        return $this->render("recipe/details/edit.html.twig", ["form" => $form->createView()]);
    }

    #[Route('/recipe/delete/{id}', name: 'delete_recipe')]
    public function deleteRecipe($id, SessionInterface $session): Response
    {
        $backRoute = $session->get('backRoute', 'recipes');
        $session->remove('backRoute');
        $recipe = $this->recipeRepository->find($id);
        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return $this->redirectToRoute($backRoute);
    }

    #[Route('/recipe/{id}', name: 'recipe', methods: ["GET"])]
    public function recipe($id, SessionInterface $session): Response
    {
        $isFromEditPage = $session->get('isFromEditPage', false);

        $session->remove('isFromEditPage');

        $recipe = $this->recipeRepository->find($id);
        $uiRecipe = UiRecipe::from($recipe);

        return $this->render('recipe/details/recipe_details.html.twig',
            [
                'recipe' => $uiRecipe,
                'isFromEditPage' => $isFromEditPage,
            ]);
    }
}
