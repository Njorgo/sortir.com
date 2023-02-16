<?php

namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreerSortieType extends AbstractType
{    
    private EntityManagerInterface $em;

    public  function __construct(EntityManagerInterface $em) {

        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Quel nom voulez vous donnez à votre sortie ?'
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie :',
                'html5' => true,
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'data' => (new \DateTime())->modify('+1 hours'),
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée de la sortie (en minute) :',
                'attr' => [
                    'min' => 5
                ]
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label'=>function (Ville $ville) {
                    return $ville->getNom();
                },
                'query_builder' => function (VilleRepository $villeRepository) {
                    return $villeRepository->createQueryBuilder('v')->orderBy('v.nom', 'ASC');
                },
                'label'=>'Ville :',
                'mapped'=>false,
                'placeholder'=>''
                ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de participants maximum :',
                'attr' => [
                    'min' => 2
                ]
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description de la sortie :'
            ])

            ->add('dateLimiteInscription', DateType::class, [
                'label' => 'Date limite d\'inscription :',
                'html5' => true,
                'widget' => 'single_text',
                'data' => (new \DateTime())->modify('+1 hours')
            ])
            ->add("Sauvegarder",SubmitType::class)
            ->add("Publier",SubmitType::class);
            
            $formModifier = function (FormInterface $form, Ville $ville = null){
                $form->add('ville', EntityType::class, [
                    'class' => Ville::class,
                    'choice_label' => 'nom',
                    'mapped' => false,
                    'placeholder' => 'Sélectionner une Ville',
                    'label' => 'ville',
                    'data'=>$ville
                ]);
    
                $lieux = (null === $ville) ? [] : $ville->getLieux();
    
                $form->add('lieuSortie', EntityType::class, [
                    'class' => Lieu::class,
                    'choices' => $lieux,
                    'choice_label' => 'nom',
                    'placeholder' => 'Sélectionner un lieu',
                    'label' => 'Lieu'
                ]);
            };
    
           $builder->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($formModifier) {
    
                    $sortie = $event->getData();
                    $lieu = $sortie->getLieuSortie() ? $sortie->getLieuSortie() : null;
                    $ville = $lieu ? $lieu->getVille() : null;
    
                    $formModifier($event->getForm(), $ville);
                }
            );
    
            $builder->addEventListener(
                FormEvents::PRE_SUBMIT,
                function (FormEvent $event) use ($formModifier) {
                    $ville = $this->em->getRepository(Ville::class)->find($event->getData()['ville']);
                    $formModifier($event->getForm(), $ville);
                }
            );
        }
    
        public function configureOptions(OptionsResolver $resolver): void
        {
            $resolver->setDefaults([
                'data_class' => Sortie::class,
            ]);
        }
    }