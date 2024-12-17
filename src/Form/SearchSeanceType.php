<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchSeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sortBy', ChoiceType::class, [
                'choices' => [
                    'Date' => 'date',
                ],
                'placeholder' => 'Tri par défaut',
                'required' => false,
            ]);
    }
}
