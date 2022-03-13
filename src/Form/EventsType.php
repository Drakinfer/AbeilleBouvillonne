<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class EventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Charger une image',
                    'data_class' => null,
                    'required' => false,
                    'empty_data' => ''
                ]
            )
            ->add('name')
            ->add('openDate')
            ->add('closeDate')
            ->add('hours')
            ->add('description')
            ->add('adress')
            ->add('postalcode')
            ->add('city')
            ->add('country');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
