<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $t = $this->translator;

        $builder
            ->add('email', TextType::class, [
                "attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                'required' => false,
            ])
            ->add('firstName', TextType::class, [
                "attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                "attr" => ["class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password', "class" => "form-input-dark w-full bg-gray-800 text-gray-100 border border-gray-600 rounded-md px-3 py-2 leading-tight focus:outline-none focus:shadow-outline"],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $t->trans('fieldRequired'),
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
