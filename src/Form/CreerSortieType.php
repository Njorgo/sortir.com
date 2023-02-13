<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use App\Entity\Campus;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Quel nom voulez vous donnez à votre sortie ?'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée de la sortie (en minute)',
                'attr' => [
                    'min' => 5
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label'=>'Nom',
                'label'=>'Ville',
                'mapped'=>false,
                'placeholder'=>''
                ])
            ->add('lieuSortie', EntityType::class, [
                'class' => Lieu::class,
                'choice_label'=>'Nom',
                'label' => 'Lieu de la sortie',
                'placeholder'=>''
            ])
            ->add('creerLieu', CreerLieuType::class, [
                'required' => false,
                'mapped' => false
            ])            
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de participants maximum',
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description de la sortie'
            ])

            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add("Sauvegarder",SubmitType::class)
            ->add("Publier",SubmitType::class);
   
    

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $events) {
                $form = $events->getForm();
                $data = $events->getData();
                if (!empty($data['creerLieu']['nom'])) {
                    $form ->remove('lieu');
                    
                    $form ->add('creerLieu', CreerLieuType::class, array(
                        'required' => true,
                        'mapped' => true,
                    ));
                }
            }
        ); 
    }

    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            "allow_extra_fields" => true,
        ]);
    }
}
