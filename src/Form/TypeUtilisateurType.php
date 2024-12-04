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

class TypeUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom'])
            ->add('description', TextType::class, ['label' => 'Description'])
            ->add('Utilisateurs', EntityType::class, [
                'class' => Utilisateur::class,
                'choice_label' => 'nom',  // or any other field to display
                'multiple' => true,  // Add if you want to allow multiple selections
            ]);
    }

   
}