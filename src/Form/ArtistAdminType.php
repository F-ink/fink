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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


class ArtistAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password', PasswordType::class, array('label' => 'Mot de passe'))
            ->add('lastname', TextType::class, array('label' => 'Nom de famille'))
            ->add('firstname', TextType::class, array('label' => 'Prénom'))
            ->add('pseudo', TextType::class, array('label' => 'Pseudo'))
            ->add('tattoo_shop', TextType::class, array('label' => 'Nom du salon'))
            ->add('city', TextType::class, array('label' => 'Ville'))
            ->add('address', TextType::class, array('label' => 'Adresse'))
            ->add('description')
            ->add('instagram', TextType::class, array('label' => 'Instagram', 'required' => false))
            ->add('siret', TextType::class, array('label' => 'Siret'))
            ->add('styles', EntityType::class, [
                'class' => Style::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'name'
            ])
            ->add('coverPicture', FileType::class, [
                'label' => 'Photo de couverture',
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('profilePicture', FileType::class, [
                'label' => 'Photo de profil',
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('created_at', DateTimeType::class, ['label' => false])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artist::class,
        ]);
    }
}
