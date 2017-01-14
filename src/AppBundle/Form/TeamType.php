<?php

namespace AppBundle\Form;

use AppBundle\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', TextType::class);
        $builder->add('lastName', TextType::class);
        $builder->add('short', TextType::class);
        $builder->add('siteName', TextType::class);
        $builder->add('city', TextType::class);
        $builder->add('state', TextType::class);
        $builder->add('conference', EntityType::class, [
            'class' => 'AppBundle\Entity\Conference',
            'placeholder' => 'Please choose a conference'
        ]);
        $builder->add('division', EntityType::class, [
            'class' => 'AppBundle\Entity\Division',
            'placeholder' => 'Please choose a division'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class
        ]);
    }

    public function getName()
    {
        return 'team_type';
    }
}
