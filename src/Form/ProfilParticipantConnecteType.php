<?php

namespace App\Form;

use App\Entity\Participant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


class ProfilParticipantConnecteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label'=> 'Pseudo :',
            ])
            ->add('nom', TextType::class, [
                'label'=> 'Nom :',
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
            ])
            ->add('telephone', TelType::class, [
                'label'=> 'Téléphone :',
            ] )
            ->add('mail', EmailType::class, [
                'label'=> 'Email :',
            ])
            ->add('Campus', TextType::class, [
                'label' => 'Campus :',
                'disabled' => true
            ])
            ->add('motPasse', PasswordType::class, [
                'label'=>'Nouveau mot de passe :',
                'mapped'=>false,
                'required'=>false,
                'constraints' => [new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit être d\'au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 180,
                    ])
                ]
            ])
            ->add('confirmation', PasswordType::class, [
                'label'=>'Confirmation :',
                'mapped'=>false,
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}