<?php

namespace App\Form;

use App\Entity\Seance;
use App\Entity\TypeSeance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SeanceformType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('nom')
            ->add('capaciteMax', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Capacité maximale',
            ])
            ->add('participantsinscrits', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Participants inscrits',
            ])
            ->add('salle')
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Programmée' => Seance::STATUT_PROGRAMME,
                    'Annulée' => Seance::STATUT_ANNULEE,
                ],
                'expanded' => false,  // Utiliser des boutons radio ou des listes déroulantes
                'multiple' => false,
            ])
            ->add('nomCoach', TextType::class, [
                'label' => 'Nom du Coach',
            ])
            ->add('objectif', ChoiceType::class, [
                'choices' => [
                    'Perdre du poids' => 'perdre du poids',
                    'Se muscler' => 'se muscler',
                    'Se défouler' => 'se défouler',
                    'Entrainement avec dance' => 'entrainement avec dance',
                    'renforcer les muscles profonds' => 'renforcer les muscles profonds',
                ],
                'placeholder' => 'Sélectionnez un objectif',
            ])
            ->add('typeSeance', EntityType::class, [
                'class' => TypeSeance::class,
                'choice_label' => 'type',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
   
}
