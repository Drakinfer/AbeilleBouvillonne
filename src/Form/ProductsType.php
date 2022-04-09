<?php

namespace App\Form;

use App\Entity\Products;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProductsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add('reference')
            ->add('description')
            ->add('prix')
            ->add('stock')
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
            ->add('poids')
            ->add('origine')
            ->add('mise_en_pot')
            ->add(
                'color',
                TextType::class,
                [
                    'label' => 'Couleur',
                ]
            )
            ->add('taille')
            ->add(
                'dlc',
                DateType::class,
                [
                    'label' => 'DLC',
                ]
            )
            ->add(
                'onOrder',
                CheckboxType::class,
                [
                    'label' => 'Sur commande',
                ]
            )
            ->add(
                'category',
                TextType::class,
                [
                    'label' => 'Catégorie de Produit',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Products::class,
        ]);
    }
}
