<?php


namespace AppBundle\Form;

use AppBundle\Entity\League;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeagueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class);
        $builder->add('description', TextareaType::class);
        $builder->add('isPrivate', ChoiceType::class, [
            'choices' => [
                'Yes' => true,
                'No' => false
            ],
            'expanded' => true,
            'multiple' => false
        ]);
        $builder->add('password', PasswordType::class);
        $builder->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => League::class
        ]);
    }

    public function getName()
    {
        return 'league_type';
    }
}