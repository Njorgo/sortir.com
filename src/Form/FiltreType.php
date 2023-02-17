<?php

namespace App\Form;

use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motCle', TextType::class, [
                'label'=> 'Le nom de la sortie contient :',
                'required'=> false,
                'attr'=> [
                    'placeholder'=> 'Rechercher'
                ]
            ])
            ->add('campus',EntityType::class, [
                'label'=> 'Campus :',
                'required'=> false,
                'class'=> Campus::class,
                'choice_label'=> 'nom'
            ])
            ->add('dateMini', DateType::class, [
                'label'=> 'Entre :',                
                'required'=> false,
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('dateMax', DateType::class, [
                'label'=> 'Et :',
                'required'=> false,
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('estOrganisee', CheckboxType::class, [
                'label' => 'Sorties dont je suis l\'organisateur',
                'required' => false,
            ])
            ->add('estInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je suis inscrit',
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'required' => false,
            ])
            ->add('sortiesFinies', CheckboxType::class, [
                'label'=> 'Sorties passées',
                'required'=> false
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'=>false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}