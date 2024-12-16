<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\IntegerType; 
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; 


class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Last Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your last name'],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'First Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your first name'],
        ])
        ->add('age', IntegerType::class, [
            'label' => 'Age',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your age'],
        ])
        ->add('genre', ChoiceType::class, [
            'label' => 'Gender',
            'choices' => [
                'Male' => 'Male',
                'Female' => 'Female',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
        ])
        ->add('statut', ChoiceType::class, [
            'label' => 'Status','choices' => [
                'Actif' => 'Actif',
                'Inactif' => 'Inactif',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
            'data' => 'Inactif', // Valeur par défaut
            'disabled' => true, // Rend le champ non modifiable
        ])
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [
                                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
