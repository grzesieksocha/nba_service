<?php declare(strict_types = 1);

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Repository\LeagueHasUserRepository;
use AppBundle\Repository\MatchRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
        /** @var LeagueHasUserRepository $lhuRepo */
        $lhuRepo = $options['lhu_repository'];
        /** @var User $user */
        $user = $options['user'];
        $leagues = $lhuRepo->getLeaguesForUser($user);

        if (count($leagues) === 1) {
            $builder
                ->add('league', HiddenType::class, [
                    'data' => $leagues[0]->getId()
                ]);
        } else {
            $choices = [];
            foreach ($leagues as $league) {
                $choices[$league->getName()] = $league->getId();
            }

            $builder
                ->add('league', ChoiceType::class, [
                    'placeholder' => 'Please pick a league',
                    'label' => 'League',
                    'choices' => $choices
                ]);
        }

        $builder
            ->add('date', ChoiceType::class, [
                'placeholder' => 'Please pick a date',
                'label' => 'Match date (mm/dd)',
                'choices' => $matchRepo->getFormattedDatesForFutureMatches()
            ])
            ->add('match', ChoiceType::class, [
                'placeholder' => 'Choose a date...',
                'disabled' => true
            ])
            ->add('player', ChoiceType::class, [
                'placeholder' => 'Then choose a match...',
                'disabled' => true
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('match_repository');
        $resolver->setRequired('lhu_repository');
        $resolver->setRequired('user');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pick_type';
    }
}
