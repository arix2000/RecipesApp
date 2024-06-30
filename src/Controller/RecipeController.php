<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\User;
use App\Form\RecipeFormType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RecipeController extends AbstractController
{
    private RecipeRepository $recipeRepository;
    private EntityManagerInterface $entityManager;
    private RequestStack $requestStack;

    public function __construct(
        RecipeRepository       $recipeRepository,
        EntityManagerInterface $entityManager,
        RequestStack           $requestStack
    )
    {
        $this->recipeRepository = $recipeRepository;
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
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
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => "root@root.com"]);
        $recipe = new Recipe();
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newRecipe = $form->getData();
            $newRecipe = $this->getUpdatedRecipe($newRecipe, $form, $user);
            $this->entityManager->persist($newRecipe);
            $this->entityManager->flush();
            return $this->redirectToRoute('recipes');
        }
        return $this->render("recipe/create.html.twig", ["form" => $form->createView()]);
    }

    #[Route('/recipe/edit/{id}', name: 'edit_recipe')]
    public function editRecipe($id, Request $request): Response
    {
        $recipe = $this->recipeRepository->find($id);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => "root@root.com"]);
        $recipe->setIngredients(implode(PHP_EOL, json_decode($recipe->getIngredients())));
        $recipe->setDirections(implode(PHP_EOL, json_decode($recipe->getDirections())));
        $recipe->setNer(implode(PHP_EOL, json_decode($recipe->getNer())));
        $form = $this->createForm(RecipeFormType::class, $recipe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $this->getUpdatedRecipe($recipe, $form, $user);
            $this->entityManager->flush();
            return $this->redirectToRoute('recipe', ["id" => $id]);
        }


        return $this->render("recipe/edit.html.twig", ["form" => $form->createView()]);
    }

    function getUpdatedRecipe(Recipe $recipe, FormInterface $form, User $user): Recipe|Response
    {
        $recipe->setUser($user);
        $recipe->setIngredients($this->stringToJsonArray($recipe->getIngredients()));
        $recipe->setDirections($this->stringToJsonArray($recipe->getDirections()));
        $recipe->setNer($this->stringToJsonArray($recipe->getNer()));

        $imagePath = $form->get('imageUrl')->getData();
        if ($imagePath) {
            $newFileName = uniqid() . '.' . $imagePath->guessExtension();
            try {
                $imagePath->move(
                    $this->getParameter('kernel.project_dir') . '/public/recipe/uploads',
                    $newFileName
                );
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            $request = $this->requestStack->getCurrentRequest();
            $hostUrl = $request->getSchemeAndHttpHost();
            $recipe->setImageUrl($hostUrl . "/recipe/uploads/" . $newFileName);
        }
        return $recipe;
    }


    function stringToJsonArray($inputString): string
    {
        $normalizedString = str_replace("\r\n", "\n", $inputString);
        $normalizedString = str_replace("\r", "\n", $normalizedString);

        $linesArray = explode("\n", $normalizedString);
        $filteredArray = array_filter($linesArray, function ($line) {
            return trim($line) !== '';
        });
        if (empty($filteredArray)) {
            $filteredArray = [$inputString];
        }
        $filteredArray = array_values($filteredArray);
        return json_encode($filteredArray);
    }

    #[Route('/recipe/delete/{id}', name: 'delete_recipe')]
    public function deleteRecipe($id): Response {
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
