<?php

namespace App\Form;

use App\Entity\Participant;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProfilParticipantConnecteType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label'=> 'Pseudo :',
                'data' => '{{app.user.pseudo}}',
                'disabled' => true
            ])
            ->add('nom', TextType::class, [
                'label'=> 'Nom :',
                'disabled' => true
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'disabled' => true
            ])
            ->add('telephone', TelType::class, [
                'label'=> 'Téléphone :',
                'disabled' => true
            ] )
            ->add('mail', EmailType::class, [
                'label'=> 'Email :',
                'disabled' => true
            ])
            ->add('motPasse', PasswordType::class, [
                'label' => 'Mot de passe :',
                'disabled' => true
            ])
            ->add('nouveauMotPasse', PasswordType::class, [
                'label' => 'Nouveau mot de passe :',
                'disabled' => true
            ])
            ->add('confirmation', PasswordType::class, [
                'label' => 'Confirmation :',
                'disabled' => true
            ])
            ->add('Campus', TextType::class, [
                'label' => 'Campus :',
                'disabled' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {


        if (null === $viewData){
            return;
        }


        //Dans le cas d'un type invalide
        if (!$viewData instanceof Participant){
            throw new UnexpectedTypeException($viewData, Participant::class);
        }

        $forms= iterator_to_array($forms);

        //initialisation des valeurs

        $forms['pseudo']->setData($viewData->getPseudo());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData)
    {
        $forms =iterator_to_array($forms);

        $viewData= new Participant(
            $forms['pseudo']->getData()
        );
    }
}
