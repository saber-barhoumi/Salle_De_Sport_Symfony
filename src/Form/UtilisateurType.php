<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\TypeUtilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;  
use Symfony\Component\Form\Extension\Core\Type\PasswordType;  
use Symfony\Component\Form\Extension\Core\Type\EmailType;  
use Symfony\Component\Form\Extension\Core\Type\IntegerType;  
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert; // Import des contraintes

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Last Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your last name'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le nom ne peut pas être vide.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[A-Za-z]+$/',  // N'accepte que des lettres
                    'message' => 'Le nom ne doit contenir que des lettres sans chiffres.',
                ]),
                new Assert\Callback([
                    'callback' => function ($object, $context) {
                        // Vérifier si la première lettre est en majuscule
                        if ($object && $object !== ucfirst($object)) {
                            $context->buildViolation('Le premier caractère du nom doit être une majuscule.')
                                ->addViolation();
                        }
                    }
                ])
            ],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'First Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your first name'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le prénom ne peut pas être vide.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[A-Za-z]+$/',  // N'accepte que des lettres
                    'message' => 'Le prénom ne doit contenir que des lettres sans chiffres.',
                ]),
                new Assert\Callback([
                    'callback' => function ($object, $context) {
                        // Vérifier si la première lettre est en majuscule
                        if ($object && $object !== ucfirst($object)) {
                            $context->buildViolation('Le premier caractère du prénom doit être une majuscule.')
                                ->addViolation();
                        }
                    }
                ])
            ],
        ])
        ->add('age', IntegerType::class, [
            'label' => 'Age',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your age'],
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'L\'âge ne peut pas être vide.'
                ]),
                new Assert\Positive([
                    'message' => 'L\'âge doit être strictement positif.'
                ]),
            ],
        ])
        ->add('genre', ChoiceType::class, [
            'label' => 'Gender',
            'choices' => [
                'Male' => 'Male',
                'Female' => 'Female',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le genre ne peut pas être vide.'
                ])
            ]
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'L\'email ne peut pas être vide.'
                ]),
                new Assert\Email([
                    'message' => 'Veuillez entrer un email valide.'
                ])
            ]
        ])
        ->add('mot_de_passe', PasswordType::class, [
            'label' => 'Mot de Passe',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le mot de passe ne peut pas être vide.'
                ])
            ]
        ])
        ->add('statut', ChoiceType::class, [
            'label' => 'Status',
            'choices' => [
                'Actif' => 'Actif',
                'Inactif' => 'Inactif',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le statut ne peut pas être vide.'
                ])
            ]
        ])
        ->add('TypeUtilisateur', EntityType::class, [
            'class' => TypeUtilisateur::class,
            'choice_label' => 'nom', 
            'label' => 'Type Utilisateur',
            'constraints' => [
                new Assert\NotBlank([
                    'message' => 'Le type utilisateur ne peut pas être vide.'
                ])
            ]
        ]);
    }
}
