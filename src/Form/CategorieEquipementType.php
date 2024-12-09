<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\CategorieEquipement;

class CategorieEquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Category Name',
                'attr' => [
                    'placeholder' => 'Enter the category name',
                    'class' => 'form-control',
                ],
            ])
            ->add('SAVE', SubmitType::class, [
                'label' => 'Save Category',
                'attr' => [
                    'class' => 'btn btn-primary btn-block mt-3',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategorieEquipement::class,
        ]);
    }
}
?>