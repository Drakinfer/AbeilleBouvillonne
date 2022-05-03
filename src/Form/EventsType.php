<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'openDate',
                DateType::class,
                [
                    'label' => "Date d'ouverture",
                ]
            )
            ->add(
                'closeDate',
                DateType::class,
                [
                    'label' => 'Date de fermeture',
                ]
            )
            ->add(
                'hours',
                DateType::class,
                [
                    'label' => "Heures d'ouvertures",
                ]
            )
            ->add('description')
            ->add(
                'adress',
                TextType::class,
                [
                    'label' => 'Adresse',
                ]
            )
            ->add(
                'postalcode',
                TextType::class,
                [
                    'label' => 'Code Postal',
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'Ville',
                ]
            )
            ->add(
                'country',
                TextType::class,
                [
                    'label' => 'Pays',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
