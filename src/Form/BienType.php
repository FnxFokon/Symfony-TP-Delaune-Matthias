<?php

namespace App\Form;

use App\Entity\Bien;
use App\Entity\User;
use App\Entity\TypeBien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ pour l'upload image
            ->add('image', FileType::class, [
                'label' => 'Image du Bien (JPG, JPEG, PNG, WEBP)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control mb-3'],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPG, JPEG, PNG, WEBP)'
                    ])
                ]
            ])
            ->add('size', TextType::class, [
                'label' => 'Taille du logement (en m2)',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du bien',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('typebien', EntityType::class, [
                'label' => 'Type de bien',
                'class' => TypeBien::class,
                'choice_label' => function (TypeBien $typeBien) {

                    if ($typeBien->getMaxPeople() == 0) {
                        $maxpeople = "";
                    } else {
                        $maxpeople = $typeBien->getMaxPeople();
                    }

                    return $typeBien->getId() . ' ' . $typeBien->getLabel() . ' ' . $maxpeople;
                },

                'attr' => ['class' => 'form-control mb-3']
            ])
            // Champ pour s'occuper du User
            // Ici on va imbriquer le formulaire de UserType
            ->add('user', EntityType::class, [
                'label' => 'Propriétaire du bien',
                'class' => User::class,
                'choice_label' => function (User $user) {

                    return $user->getId() . ' ' . $user->getFirstname() . ' ' . $user->getLastname() . ' - ' . $user->getEmail();
                },
                'attr' => ['class' => 'form-control mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bien::class,
            'attr' => ['class' => 'form']
        ]);
    }
}
