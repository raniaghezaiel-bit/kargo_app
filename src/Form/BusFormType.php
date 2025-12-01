<?php

namespace App\Form;

use App\Entity\Bus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BusType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'Numéro de Matricule',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: TUN-1234-AB'
                ]
            ])
            ->add('capacite', IntegerType::class, [
                'label' => 'Capacité (nombre de places)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: 40',
                    'min' => 10,
                    'max' => 100
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de Bus',
                'choices' => [
                    'Standard' => 'Standard',
                    'VIP' => 'VIP',
                    'Climatisé' => 'Climatisé',
                    'Couchette' => 'Couchette',
                    'Économique' => 'Économique'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['is_edit'] ? 'Modifier le Bus' : 'Ajouter le Bus',
                'attr' => [
                    'class' => 'btn btn-primary mt-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bus::class,
            'is_edit' => false,
        ]);
    }
}