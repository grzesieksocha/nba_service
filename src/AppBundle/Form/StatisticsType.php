<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\Statistics;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StatisticsType
 * @package AppBundle\Form
 */
class StatisticsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('player', TextType::class);
        $builder->add('match', TextType::class, [
            'mapped' => false
        ]);
        $builder->add('minutes', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('points', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('rebounds', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('assists', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('blocks', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('steals', IntegerType::class, [
            'data' => 0
        ]);
        $builder->add('turnovers', IntegerType::class, [
            'data' => 0
        ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Statistics::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'statistics_type';
    }
}