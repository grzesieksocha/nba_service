<?php

namespace AppBundle\Form;

use AppBundle\Entity\Match;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('homeTeam', EntityType::class, [
                'class' => 'AppBundle\Entity\Team',
                'placeholder' => 'Please choose a home team'
            ])
            ->add('awayTeam', EntityType::class, [
                'class' => 'AppBundle\Entity\Team',
                'placeholder' => 'Please choose an away team'
            ])
            ->add('date', DateTimeType::class, [
                'data' => new DateTime()
            ])
            ->add('homeTeamPoints', IntegerType::class, [
                'required' => false
            ])
            ->add('awayTeamPoints', IntegerType::class, [
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Match::class
        ]);
    }

    public function getName()
    {
        return 'team_type';
    }
}
