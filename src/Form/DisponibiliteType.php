<?php

namespace App\Form;

use App\Entity\Disponibilite;
use App\Entity\Vehicule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DisponibiliteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', null, [
                'widget' => 'single_text',
                'label' => 'La date de début',
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text',
                'label' => 'La date de fin',
            ])
            ->add('prixParJour', IntegerType::class, [
                'label' => 'Le prix par jour',
            ])
            ->add('statut', TextType::class, [
                'label' => 'Le statut (O pour disponible et N pour non disponible)',
            ])
            ->add('idVehicule', EntityType::class, [
                'class' => Vehicule::class,
                'label' => 'Le véhicule associé',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Disponibilite::class,
        ]);
    }
}
