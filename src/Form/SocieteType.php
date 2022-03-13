<?php

namespace App\Form;

use App\Entity\Societe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class SocieteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('forme')
            ->add('adresse')
            ->add('codepostal')
            ->add('ville')
            ->add('pays')
            ->add('siret')
            ->add('telephone')
            ->add('mail')
            ->add('portable')
            ->add(
                'logo',
                FileType::class,
                [
                    'label' => 'Charger un logo',
                    'data_class' => null,
                    'required' => false,
                    'empty_data' => ''
                ]
            )
            ->add(
                'banniere',
                FileType::class,
                [
                    'label' => 'Charger une banniere',
                    'data_class' => null,
                    'required' => false,
                    'empty_data' => ''
                ]
            )
            ->add(
                'favicon',
                FileType::class,
                [
                    'label' => 'Charger une favicon',
                    'data_class' => null,
                    'required' => false,
                    'empty_data' => ''
                ]
            );;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Societe::class,
        ]);
    }
}
