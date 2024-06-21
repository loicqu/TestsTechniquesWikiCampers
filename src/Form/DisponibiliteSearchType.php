<?php

namespace App\Form;

use App\Entity\DisponibiliteSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibiliteSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Date de début'
                ]
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Date de fin'
                ]
            ])
            ->add('prixMax', IntegerType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prix maximum de la location'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DisponibiliteSearch::class,
            'method' => 'get',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(){
        return ''; # permet d'avoir une URL plus propre
    }
}
