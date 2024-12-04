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
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, ['label' => 'Nom'])
        ->add('prenom', TextType::class, ['label' => 'Prenom'])
        ->add('mot_de_passe', PasswordType::class, ['label' => 'Mot de Passe'])
        ->add('age', IntegerType::class, ['label' => 'Age'])
        ->add('genre', TextType::class, ['label' => 'Genre'])
        ->add('email', EmailType::class, ['label' => 'Email'])
        ->add('statut', TextType::class, ['label' => 'Statut'])
        ->add('TypeUtilisateur', EntityType::class, [
            'class' => TypeUtilisateur::class,
            'choice_label' => 'nom', 
            'label' => 'Type Utilisateur'
        ]);
    }

}
