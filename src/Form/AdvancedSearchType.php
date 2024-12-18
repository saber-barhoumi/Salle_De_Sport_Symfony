<?php

namespace App\Form;

use App\Entity\CategorieProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // <-- Make sure this is correctly imported
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AdvancedSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom du produit'
            ])
            ->add('CategorieProduit', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
                'required' => false,
                'label' => 'Catégorie'
           
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, // Pas besoin de lier à une entité spécifique

        ]);

    }
}
