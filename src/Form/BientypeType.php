<?php

namespace App\Form;

use App\Entity\TypeBien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BientypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => 'Type de bien',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix de location du bien',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('maxPeople', TextType::class, [
                'label' => 'Nombre de Places',
                'attr' => ['class' => 'form-control mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TypeBien::class,
            'attr' => ['class' => 'form']
        ]);
    }
}
