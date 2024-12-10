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

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'label' => 'Last Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your last name'],
        ])
        ->add('prenom', TextType::class, [
            'label' => 'First Name',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your first name'],
        ])
        ->add('age', IntegerType::class, [
            'label' => 'Age',
            'attr' => ['class' => 'form-control', 'placeholder' => 'Enter your age'],
        ])
        ->add('genre', ChoiceType::class, [
            'label' => 'Gender',
            'choices' => [
                'Male' => 'Male',
                'Female' => 'Female',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
        ])
        ->add('email', EmailType::class, ['label' => 'Email'])
        ->add('mot_de_passe', PasswordType::class, ['label' => 'Mot de Passe'])
        ->add('statut', ChoiceType::class, [
            'label' => 'Status','choices' => [
                'Actif' => 'Actif',
                'Inactif' => 'Inactif',
            ],
            'expanded' => true, // Display as radio buttons
            'multiple' => false,
        ])
        ->add('TypeUtilisateur', EntityType::class, [
            'class' => TypeUtilisateur::class,
            'choice_label' => 'nom', 
            'label' => 'Type Utilisateur'
        ]);
    }

}
