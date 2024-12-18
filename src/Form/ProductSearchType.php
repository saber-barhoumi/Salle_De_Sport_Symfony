<?php
// src/Form/ProductSearchType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', IntegerType::class, [
                'label' => 'ID du produit',
                'required' => false,
            ])
            ->add('categorie', EntityType::class, [
                'label' => 'Catégorie',
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
                'required' => false,
                'placeholder' => 'Sélectionner une catégorie',
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
