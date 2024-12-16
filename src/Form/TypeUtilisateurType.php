<?php

namespace App\Form;

use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\TypeUtilisateur;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;  // Import des contraintes

class TypeUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    // Vérifie que le nom ne contient pas de chiffres
                    new Assert\Regex([
                        'pattern' => '/^[A-Za-z\s\-]+$/', // Accepte uniquement des lettres, espaces et tirets
                        'message' => 'Le nom ne doit contenir que des lettres, des espaces et des tirets.',
                    ]),
                    // Vérifie que le premier caractère du nom est une majuscule
                    new Assert\Callback([
                        'callback' => function ($object, $context) {
                            // Si le nom commence par une minuscule, ajoute une erreur
                            if ($object && $object !== ucfirst($object)) {
                                $context->buildViolation('Le premier caractère du nom doit être une majuscule.')
                                    ->addViolation();
                            }
                        }
                    ])
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'constraints' => [
                    // Aucune validation HTML5 ici, juste la validation côté serveur
                ],
            ])
            ->add('Utilisateurs', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',  // Afficher le nom de l'utilisateur
                'multiple' => true,  // Permet plusieurs sélections
                'constraints' => [
                    // Aucune validation HTML5 ici, juste la validation côté serveur
                ],
            ]);
    }
}
