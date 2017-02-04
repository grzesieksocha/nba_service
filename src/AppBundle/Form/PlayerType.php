<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\Player;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PlayerType
 * @package AppBundle\Form
 */
class PlayerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('number', IntegerType::class)
            ->add('team', EntityType::class, [
                'class' => 'AppBundle\Entity\Team',
                'placeholder' => 'Please choose Player\'s team'
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Player::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'player_type';
    }
}
