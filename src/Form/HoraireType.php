<?php

namespace App\Form;

use App\Entity\Bus;
use App\Entity\Horaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class HoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ============================================
            // SECTION 1 : Informations du trajet
            // ============================================
            ->add('villeDepart', ChoiceType::class, [
                'label' => 'Ville de départ',
                'choices' => $this->getVillesTunisiennes(),
                'placeholder' => '-- Sélectionnez la ville de départ --',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une ville de départ'
                    ])
                ]
            ])
            
            ->add('villeArrivee', ChoiceType::class, [
                'label' => 'Ville d\'arrivée',
                'choices' => $this->getVillesTunisiennes(),
                'placeholder' => '-- Sélectionnez la ville d\'arrivée --',
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une ville d\'arrivée'
                    ])
                ]
            ])
            
            ->add('distance', IntegerType::class, [
                'label' => 'Distance (km)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 270',
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 1000
                ],
                'help' => 'Distance approximative entre les deux villes en kilomètres',
                'constraints' => [
                    new Assert\Positive([
                        'message' => 'La distance doit être supérieure à 0'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 1000,
                        'message' => 'La distance ne peut pas dépasser 1000 km'
                    ])
                ]
            ])
            
            // ============================================
            // SECTION 2 : Horaires
            // ============================================
            ->add('heureDepart', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'help' => 'Format 24h (ex: 08:30)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'heure de départ est obligatoire'
                    ])
                ]
            ])
            
            ->add('heureArrivee', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
                'help' => 'Format 24h (ex: 12:00)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'heure d\'arrivée est obligatoire'
                    ])
                ]
            ])
            
            // ============================================
            // SECTION 3 : Jours de circulation
            // ============================================
            ->add('joursActifs', ChoiceType::class, [
                'label' => false, // Le label est géré dans le template
                'choices' => [
                    'Lundi' => 'lundi',
                    'Mardi' => 'mardi',
                    'Mercredi' => 'mercredi',
                    'Jeudi' => 'jeudi',
                    'Vendredi' => 'vendredi',
                    'Samedi' => 'samedi',
                    'Dimanche' => 'dimanche',
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                'constraints' => [
                    new Assert\Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez sélectionner au moins un jour de circulation'
                    ])
                ],
                'help' => 'Sélectionnez au moins un jour'
            ])
            
            // ============================================
            // SECTION 4 : Prix et configuration
            // ============================================
            ->add('prix', MoneyType::class, [
                'label' => 'Prix du billet',
                'currency' => 'TND',
                'attr' => [
                    'placeholder' => 'Ex: 15.000',
                    'class' => 'form-control',
                    'step' => '0.001',
                    'min' => 0.001
                ],
                'required' => true,
                'help' => 'Prix en dinars tunisiens (TND)',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le prix est obligatoire'
                    ]),
                    new Assert\Positive([
                        'message' => 'Le prix doit être supérieur à 0'
                    ]),
                    new Assert\LessThanOrEqual([
                        'value' => 500,
                        'message' => 'Le prix ne peut pas dépasser 500 TND'
                    ])
                ]
            ])
            
            ->add('bus', EntityType::class, [
                'label' => 'Bus assigné',
                'class' => Bus::class,
                'choice_label' => function(Bus $bus) {
                    return sprintf(
                        '%s (%d places)', 
                        $bus->getNumero(), 
                        $bus->getCapacite()
                    );
                },
                'required' => false,
                'placeholder' => '-- Aucun bus assigné (optionnel) --',
                'attr' => [
                    'class' => 'form-select'
                ],
                'help' => 'Vous pouvez assigner un bus plus tard'
            ])
            
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif - Visible et réservable' => 'actif',
                    'Inactif - Non visible aux clients' => 'inactif',
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'required' => true,
                'help' => 'Les horaires inactifs ne sont pas visibles aux clients',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le statut est obligatoire'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horaire::class,
            'attr' => [
                'novalidate' => 'novalidate' // Pour utiliser la validation HTML5
            ]
        ]);
    }

    /**
     * Liste complète des villes tunisiennes (24 gouvernorats)
     * Ordonnée alphabétiquement pour faciliter la recherche
     */
    private function getVillesTunisiennes(): array
    {
        return [
            'Ariana' => 'Ariana',
            'Béja' => 'Béja',
            'Ben Arous' => 'Ben Arous',
            'Bizerte' => 'Bizerte',
            'Gabès' => 'Gabès',
            'Gafsa' => 'Gafsa',
            'Jendouba' => 'Jendouba',
            'Kairouan' => 'Kairouan',
            'Kasserine' => 'Kasserine',
            'Kébili' => 'Kébili',
            'Le Kef' => 'Le Kef',
            'Mahdia' => 'Mahdia',
            'Manouba' => 'Manouba',
            'Médenine' => 'Médenine',
            'Monastir' => 'Monastir',
            'Nabeul' => 'Nabeul',
            'Sfax' => 'Sfax',
            'Sidi Bouzid' => 'Sidi Bouzid',
            'Siliana' => 'Siliana',
            'Sousse' => 'Sousse',
            'Tataouine' => 'Tataouine',
            'Tozeur' => 'Tozeur',
            'Tunis' => 'Tunis',
            'Zaghouan' => 'Zaghouan'
        ];
    }
}