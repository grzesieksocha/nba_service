<?php declare(strict_types = 1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use AppBundle\Entity\LeagueOptions;

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
        $builder->add('doCountPoints', CheckboxType::class, [
            'data' => true,
            'required' => false
        ]);
        $builder->add('doCountRebounds', CheckboxType::class, [
            'data' => true,
            'required' => false
        ]);
        $builder->add('doCountAssists', CheckboxType::class, [
            'data' => true,
            'required' => false
        ]);
        $builder->add('doCountBlocks', CheckboxType::class, [
            'data' => false,
            'required' => false
        ]);
        $builder->add('doCountSteals', CheckboxType::class, [
            'data' => false,
            'required' => false
        ]);
        $builder->add('firstRoundMultiplier', TextType::class, [
            'data' => 1
        ]);
        $builder->add('secondRoundMultiplier', TextType::class, [
            'data' => 1
        ]);
        $builder->add('thirdRoundMultiplier', TextType::class, [
            'data' => 1
        ]);
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