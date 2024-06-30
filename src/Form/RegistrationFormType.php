<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class,
                ["attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'This field is required.',
                        ]),
                        new Email(message: "Please enter a valid email address."),
                    ]
                ])
            ->add('firstName', TextType::class,
                ["attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'This field is required.',
                        ]),
                    ],
                ])
            ->add('lastName', TextType::class,
                ["attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'This field is required.',
                        ]),
                    ],
                ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', "class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'This field is required.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
