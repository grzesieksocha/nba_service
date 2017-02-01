<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\Pick;

use AppBundle\Repository\MatchRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PickType
 * @package AppBundle\Form
 */
class PickType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var MatchRepository $matchRepo */
        $matchRepo = $options['match_repository'];

        $builder
            ->add('date', ChoiceType::class, [
                'placeholder' => 'Please pick a date',
                'label' => 'Match date (mm/dd)',
                'choices' => $matchRepo->getFormattedDatesForFutureMatches()
            ])
            ->add('match', ChoiceType::class, [
                'mapped' => false,
                'placeholder' => 'Waiting for a date...',
                'disabled' => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Pick::class
        ]);

        $resolver->setRequired('match_repository');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pick_type';
    }
}
