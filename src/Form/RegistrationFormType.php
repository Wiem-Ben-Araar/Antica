<?php

namespace App\Form;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'attr' => ['autocomplete' => 'new-nom'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre nom!',
                    ]),
                ],
            ])

            ->add('prenom', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre prenom!',
                    ]),
                ],
            ])
            ->add('email', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email cannot be empty',
                    ]),
                    new Regex([
                        'pattern' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/', // expression régulière à vérifier
                        'message' => 'Email address invalid!',
                    ]),
                ],
            ])

            ->add('telephone', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre telephone!',
                    ]),
                ],
            ])
            ->add('adresse', null, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre adresse!',
                    ]),
                ],
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],

            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}