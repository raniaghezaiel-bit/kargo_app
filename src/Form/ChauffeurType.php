<?php

namespace App\Form;

use App\Entity\Chauffeur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ChauffeurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // NOUVEAU : Champ photo
            ->add('photoFile', FileType::class, [
                'label' => 'Photo du chauffeur',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'accept' => 'image/*'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPG, PNG)',
                        'maxSizeMessage' => 'L\'image est trop volumineuse ({{ size }} {{ suffix }}). Maximum autorisé : {{ limit }} {{ suffix }}.',
                    ])
                ],
                'help' => 'Formats acceptés : JPG, PNG. Taille max : 2 Mo'
            ])
            
            // Informations personnelles
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Ex: Ben Ali',
                    'class' => 'form-control'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Ex: Mohamed',
                    'class' => 'form-control'
                ]
            ])
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'attr' => [
                    'placeholder' => '12345678',
                    'maxlength' => 8,
                    'class' => 'form-control'
                ]
            ])
            ->add('dateNaissance', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'html5' => true
            ])
            
            // Coordonnées
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => '12345678',
                    'maxlength' => 8,
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'chauffeur@exemple.com',
                    'class' => 'form-control'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Rue, numéro...',
                    'class' => 'form-control'
                ]
            ])
            ->add('ville', ChoiceType::class, [
                'label' => 'Ville',
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Sfax' => 'Sfax',
                    'Sousse' => 'Sousse',
                    'Kairouan' => 'Kairouan',
                    'Bizerte' => 'Bizerte',
                    'Gabès' => 'Gabès',
                    'Ariana' => 'Ariana',
                    'Gafsa' => 'Gafsa',
                    'Monastir' => 'Monastir',
                    'Ben Arous' => 'Ben Arous',
                    'Kasserine' => 'Kasserine',
                    'Médenine' => 'Médenine',
                    'Nabeul' => 'Nabeul',
                    'Tataouine' => 'Tataouine',
                    'Béja' => 'Béja',
                    'Jendouba' => 'Jendouba',
                    'Mahdia' => 'Mahdia',
                    'Sidi Bouzid' => 'Sidi Bouzid',
                    'Siliana' => 'Siliana',
                    'Kébili' => 'Kébili',
                    'Le Kef' => 'Le Kef',
                    'Tozeur' => 'Tozeur',
                    'Manouba' => 'Manouba',
                    'Zaghouan' => 'Zaghouan'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'placeholder' => 'Sélectionnez une ville'
            ])
            
            // Informations sur le permis
            ->add('numeroPermis', TextType::class, [
                'label' => 'Numéro de permis',
                'attr' => [
                    'placeholder' => 'Ex: TN123456789',
                    'class' => 'form-control'
                ]
            ])
            ->add('typePermis', ChoiceType::class, [
                'label' => 'Type de permis',
                'choices' => [
                    'Permis B (Véhicules légers)' => 'B',
                    'Permis C (Poids lourds)' => 'C',
                    'Permis D (Transport en commun)' => 'D',
                    'Permis E (Remorque)' => 'E'
                ],
                'attr' => [
                    'class' => 'form-control'
                ],
                'placeholder' => 'Sélectionnez le type'
            ])
            ->add('dateDelivrancePermis', DateType::class, [
                'label' => 'Date de délivrance du permis',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'html5' => true
            ])
            ->add('dateExpirationPermis', DateType::class, [
                'label' => 'Date d\'expiration du permis',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ],
                'html5' => true
            ])
            
            // Statut et notes
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'Actif' => 'actif',
                    'Inactif' => 'inactif',
                    'En congé' => 'en_conge',
                    'Suspendu' => 'suspendu'
                ],
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Informations supplémentaires...',
                    'rows' => 4,
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chauffeur::class,
        ]);
    }
}