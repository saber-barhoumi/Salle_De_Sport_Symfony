<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use App\Entity\CategorieProduit;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Tag; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du Produit',
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ]) // Champ nom
            ->add('description', TextType::class, [ // Champ description
                'label' => 'Description',
                'required' => true,
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ])
            ->add('prix', TextType::class, [
                'label' => 'Prix',
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ]) // Champ prix
            ->add('quantiteStock', TextType::class, [
                'label' => 'Quantité en Stock',
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ]) // Champ quantité en stock
            ->add('CategorieProduit', EntityType::class, [ // Liste déroulante pour CategorieProduit
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du Produit',
                'required' => false,
                'mapped' => false, // Permet de ne pas lier directement ce champ à la propriété "image"
                'attr' => [
                    'accept' => 'image/*',
                    'novalidate' => 'novalidate'  // Désactive la validation HTML5
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true, // Optional for checkboxes
                'attr' => ['novalidate' => 'novalidate']  // Désactive la validation HTML5
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class, // Classe de l'entité associée
        ]);
    }
}
