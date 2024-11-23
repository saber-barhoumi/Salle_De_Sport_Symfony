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
use Symfony\Component\Form\Extension\Core\Type\TextType; // Correct import
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('image', FileType::class, [
                'label' => 'Image du produit (JPEG/PNG uniquement)',
                'mapped' => false, // Ne pas lier ce champ directement à l'entité
                'required' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG ou PNG).',
                    ]),
                ],
            ])
            ->add('prix', TextType::class, [
                'label' => 'Prix du produit',
                'required' => true,
            ])
            ->add('quantiteStock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'required' => true,
                'constraints' => [
                    new NotNull([
                        'message' => 'La quantité en stock ne peut pas être vide.',
                    ]),
                ],
            ])
            ->add('CategorieProduit', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
