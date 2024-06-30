<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class RecipeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Title is required'),
                ],
                'attr' => ['class' => 'form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline']
            ])
            ->add('ingredients', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Ingredients is required'),
                ],
                'attr' => ['class' => 'form-textarea-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'rows' => 5, 'placeholder' => '(Write each ingredient in a separate line)']
            ])
            ->add('directions', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Directions is required'),
                ],
                'attr' => ['class' => 'form-textarea-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'rows' => 8, 'placeholder' => '(Write each direction in a separate line)']
            ])
            ->add('source', ChoiceType::class, [
                'label' => 'Source:',
                'choices' => [
                    'From website' => 'From website',
                    'From book' => 'From book',
                    'My Own' => 'My Own',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => ['class' => 'form-radio-dark mb-4 ml-4 bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline'],
                'data' => 'From website',
            ])
            ->add('link', TextType::class, [
                'constraints' => [
                    new Url(['message' => 'This is not a valid URL']),
                ],
                'required' => false,
                'attr' => ['class' => 'tooltip-trigger form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'style' => 'display:none;'],
            ])
            ->add('site', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Url(['message' => 'This is not a valid URL']),
                ],
                'attr' => ['class' => 'form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'style' => 'display:none;'],

            ])
            ->add('ner', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(message: 'Ingredients (no quantities) is required'),
                ],
                'attr' => ['class' => 'form-textarea-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'rows' => 8, 'placeholder' => '(Write each ingredient in a separate line)']
            ])
            ->add('imageUrl', FileType::class, [
                'label' => 'Add image',
                'required' => false,
                'attr' => ['class' => 'form-textarea-dark w-full mb-4 bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline', 'rows' => 8]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'validation_groups' => ['Default', 'OptionalUrl']
        ]);
    }
}
