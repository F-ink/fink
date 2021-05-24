<?php

namespace App\Form;

use App\Entity\Artist;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Style;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class ArtistAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password')
            ->add('lastname')
            ->add('firstname')
            ->add('pseudo')
            ->add('tattoo_shop')
            ->add('city')
            ->add('address')
            ->add('description')
            ->add('instagram')
            ->add('siret')
            ->add('styles', EntityType::class, [
                'class' => Style::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'
            ])
            ->add('coverPicture', FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' =>false,
                'required' => false
            ])
            ->add('created_at', DateType::class)
            ->add('Valider', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
