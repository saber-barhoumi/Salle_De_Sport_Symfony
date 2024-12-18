<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\Typeabonnement;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour l'autorenouvellement (case à cocher)
            ->add('autorenouvellement', CheckboxType::class, [
                'label' => 'Renouvellement automatique',
                'required' => false,
            ])
            
            // Type d'abonnement (relation avec Typeabonnement via EntityType)
            ->add('typeAbonnement', EntityType::class, [
                'class' => Typeabonnement::class,
                'choice_label' => 'nom', // Supposons que "nom" est une propriété de Typeabonnement
                'label' => 'Type d\'abonnement',
                'placeholder' => 'Sélectionnez un type d\'abonnement',
                'required' => true,
            ])
            
            // Champ pour le prix
            ->add('prix', NumberType::class, [
                'label' => 'Prix de l\'abonnement (€)',
                'required' => true,
                'attr' => [
                    'min' => 0, // Valeur minimale pour éviter les erreurs
                    'step' => 0.01, // Précision pour les décimales
                ],
            ])
            
            // Champ pour le sport
            ->add('sport', TextType::class, [
                'label' => 'Sport associé',
                'required' => true,
                'attr' => [
                    'maxlength' => 50,
                    'placeholder' => 'Entrez le nom du sport',
                ],
            ])
            
            // Champ pour la capacité
            ->add('capacite', NumberType::class, [
                'label' => 'Capacité maximale',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ])
            
            // Champ pour les commentaires
            ->add('commentaires', TextareaType::class, [
                'label' => 'Commentaires',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'Ajoutez des informations complémentaires (facultatif)',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
