<?php

namespace App\Form;

use App\Entity\Reservation;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début du séjour',
                'attr' => ['class' => 'form-control mb-3 datepicker'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin du séjour',
                'attr' => ['class' => 'form-control mb-3 datepicker'],
            ])
            ->add('people', ChoiceType::class, [
                'choices' => $this->numberOfPeople(),
                'label' => 'Combien de personne',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('children', ChoiceType::class, [
                'choices' => $this->numberOfPeople(),
                'label' => 'Combien d\'enfant',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('baby', ChoiceType::class, [
                'choices' => $this->numberOfPeople(),
                'label' => 'Combien de Bébé',
                'attr' => ['class' => 'form-control mb-3']
            ])
            ->add('accessPool', ChoiceType::class, [
                'choices' => [
                    'Oui' => '1',
                    'Non' => '0'
                ],
                'label' => 'Accés à la piscine',
                'attr' => ['class' => 'form-control mb-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
            'attr' => ['class' => 'form']
        ]);
    }

    public function numberOfPeople(): array
    {
        $choices = [];
        for ($i = 0; $i <= 20; $i++) {
            $choices[$i] = $i;
        }
        return $choices;
    }
}
