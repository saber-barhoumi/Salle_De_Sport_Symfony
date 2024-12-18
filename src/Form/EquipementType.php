<?php

namespace App\Form;

use App\Entity\CategorieEquipement;
use App\Entity\Equipement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Equipment Name',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('prix', TextType::class, [
                'label' => 'Price (€)',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'Condition',
                'choices' => ['New' => 'New', 'Used' => 'Used'],
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-check'],
            ])
            ->add('fournisseur', TextType::class, [
                'label' => 'Supplier',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Equipement', EntityType::class, [
                'class' => CategorieEquipement::class,
                'choice_label' => 'nom',
                'label' => 'Category',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('descriptions', TextareaType::class, [
                'label' => 'Descriptions',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Enter a detailed description...',
                ],
            ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Equipement::class]);
    }
}
