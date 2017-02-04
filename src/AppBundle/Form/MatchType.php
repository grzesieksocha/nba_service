<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\Match;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

use \DateTime;

/**
 * Class MatchType
 * @package AppBundle\Form
 */
class MatchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
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
                'widget' => 'single_text',
                'label' => 'Match date (ET)',
//                'data' => new DateTime(),
                'attr' => [
                    'class' => 'js-datepicker'
                ]
            ])
            ->add('homeTeamPoints', IntegerType::class, [
                'data' => 0,
                'required' => false
            ])
            ->add('awayTeamPoints', IntegerType::class, [
                'data' => 0,
                'required' => false
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var Match $match */
            $match = $event->getData();
            $form = $event->getForm();

            if ($match && null !== $match->getId()) {
                foreach ($match->getAwayTeam()->getPlayers() as $player) {
                    $form->add('stats_' . $player->getId(), StatisticsType::class, [
                        'mapped' => false,
                        'required' => false
                    ]);
                }
                foreach ($match->getHomeTeam()->getPlayers() as $player) {
                    $form->add('stats_' . $player->getId(), StatisticsType::class, [
                        'mapped' => false,
                        'required' => false
                    ]);
                }
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Match::class
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'team_type';
    }
}
