<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
// use App\Entity\Style;


class ArtistAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            // ->add('roles')
            ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('pseudo')
            ->add('tattoo_shop')
            ->add('city')
            ->add('address')
            ->add('profile_picture')
            ->add('description')
            ->add('instagram')
            ->add('siret')
            ->add('styles', EntityType::class,['class' => Style::class,
             'choice_label' => 'Votre style',
              'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('s');
                
            }])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
