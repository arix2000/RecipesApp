<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeFormType;
use App\Repository\RecipeRepository;
use App\Services\RecipeService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private EntityManagerInterface $entityManager;
    private RecipeService $recipeService;

    public function __construct(
        RecipeRepository       $recipeRepository,
        EntityManagerInterface $entityManager,
        RequestStack           $requestStack,
        string                 $projectDir
    )
    {
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $request = $requestStack->getCurrentRequest();
        $hostUrl = $request->getSchemeAndHttpHost();
        $this->recipeService = new RecipeService($recipeRepository, $projectDir, $hostUrl);
    }

    #[Route('/', name: 'recipes')]
    public function recipes(Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $this->recipeService->getRecipePagination($request, $paginator)->getPagination();

        if ($request->isXmlHttpRequest()) {
            return $this->render('recipe/recipes_list.html.twig', ['pagination' => $pagination]);
        }

        return $this->render('recipe/index.html.twig', [
            'pagination' => $pagination,
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
            return $this->redirectToRoute('recipes');
        }
        return $this->render("recipe/create.html.twig", ["form" => $form->createView()]);
    }

    #[Route('/recipe/edit/{id}', name: 'edit_recipe')]
    public function editRecipe($id, Request $request): Response
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
            return $this->redirectToRoute('recipe', ["id" => $id]);
        }


        return $this->render("recipe/edit.html.twig", ["form" => $form->createView()]);
    }

    #[Route('/recipe/delete/{id}', name: 'delete_recipe')]
    public function deleteRecipe($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return $this->redirectToRoute('recipes');
    }

    #[Route('/recipe/{id}', name: 'recipe', methods: ["GET"])]
    public function recipe($id): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $ner = implode(", ", json_decode($recipe->getNer()));
        $directions = json_decode($recipe->getDirections());
        $ingredients = json_decode($recipe->getIngredients());

        return $this->render('recipe/recipe_details.html.twig',
            [
                'recipe' => $recipe,
                'ner' => $ner,
                'directions' => $directions,
                'ingredients' => $ingredients,
            ]);
    }

    #[Route('/your-recipes', name: 'your_recipes')]
    public function yourRecipes(): Response
    {
        return $this->render('');
    }

    #[Route('/search', name: 'search')]
    public function search(): Response
    {
        return $this->render('');
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('');
    }
}
