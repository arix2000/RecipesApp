<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeFormType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(RecipeRepository $recipeRepository, EntityManagerInterface $entityManager)
    {
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'recipes')]
    public function recipes(): Response
    {
        $recipes = $this->recipeRepository->findAll();
        $recipes = array_slice($recipes, 0, 20);
        array_map(function ($recipe) {
            $readableString = implode(", ", json_decode($recipe->getNer()));
            $recipe->setNer($readableString);
        }, $recipes);
        return $this->render('recipe/index.html.twig', ["recipes" => $recipes]);
    }

    #[Route('/recipe/create', name: 'create_recipe')]
    public function createRecipe(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRecipe = $form->getData();
            $imagePath = $form->get('imageUrl')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                } //ADD USER THEN TEST
                $newRecipe->setImage("uploads/" . $newFileName);
            }

            $this->entityManager->persist($newRecipe);
            $this->entityManager->flush();
            return $this->redirectToRoute('recipes');
        }
        return $this->render("recipe/create.html.twig", ["form" => $form->createView()]);
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
                "recipe" => $recipe,
                "ner" => $ner,
                "directions" => $directions,
                "ingredients" => $ingredients,
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
