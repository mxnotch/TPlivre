<?php

namespace App\Form;

use App\Entity\Emprunt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Adherent;
use App\Entity\livre;
use App\Repository\LivreRepository;

class EmpruntType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_emprunt')
            ->add('date_fin_prevue')
            ->add('date_retour')
            ->add('adherent', EntityType::class, [
                'class'=>Adherent::class,
                'choice_label' => 'nom'
            ])
            ->add('livre', EntityType::class, [
                'class' => Livre::class,
                'choice_label' => 'titre',
                'query_builder' => function (LivreRepository $livreRepository) {
                    return $livreRepository->createQueryBuilder('l')
                        ->where('l.desactive != 1')
                        ->orderBy('l.id', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emprunt::class,
        ]);
    }
}
