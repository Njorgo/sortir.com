<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerLieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom du lieu'
                ]
            ])
            ->add('rue', TextType::class, [
                'attr' => [
                    'placeholder' => 'Adresse du lieu'
                ]
            ])
            ->add('latitude', HiddenType::class, [
                'attr' => [
                    'placeholder' => 'Latitude du lieu'
                ]
            ])
            ->add('longitude', HiddenType::class, [
                'attr' => [
                    'placeholder' => 'Longitude du lieu'
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'Nom',
                'placeholder' => 'choisissez une ville',
                'label' => 'Ville :',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ville',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
