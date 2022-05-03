<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'first_name',
                TextType::class,
                [
                    'label' => 'Prénom',
                ]
            )
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
            )
            ->add(
                'phone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
