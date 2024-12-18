<?php

namespace App\Form;

use App\Entity\CategorieProduit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Importation pour les types spécifiques
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [ // Définition du champ avec plus de détails
                'label' => 'Nom de la catégorie', // Label personnalisé
                'attr' => [
                    'class' => 'form-control', // Classe CSS pour le styling
                    'placeholder' => 'Entrez le nom de la catégorie', // Placeholder
                ],
                'required' => true, // Validation obligatoire
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieProduit::class,
        ]);
    }
}
