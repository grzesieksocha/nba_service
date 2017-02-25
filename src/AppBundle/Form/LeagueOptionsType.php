<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\LeagueOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LeagueOptionsType
 * @package AppBundle\Form
 */
class LeagueOptionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('league', HiddenType::class);
        $builder->add('doCountPoints', ChoiceType::class);
        $builder->add('doCountRebounds', ChoiceType::class);
        $builder->add('doCountAssists', ChoiceType::class);
        $builder->add('doCountBlocks', ChoiceType::class);
        $builder->add('doCountSteals', ChoiceType::class);
        $builder->add('firstRoundMultiplier', IntegerType::class);
        $builder->add('secondRoundMultiplier', IntegerType::class);
        $builder->add('thirdRoundMultiplier', IntegerType::class);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LeagueOptions::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'league_options_type';
    }
}