<?php declare(strict_types = 1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\League;

/**
 * Class LeagueType
 * @package AppBundle\Form
 */
class LeagueType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
            'multiple' => false,
            'data' => false
        ]);
        $builder->add('password', PasswordType::class, [
            'required' => false,
            'attr' => [
                'display' => 'none'
            ]
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => League::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'league_type';
    }
}