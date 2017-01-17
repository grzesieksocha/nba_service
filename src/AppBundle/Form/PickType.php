<?php

namespace AppBundle\Form;

use AppBundle\Entity\Pick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $matchRepo = $options['match_repository'];

        $builder
            ->add('date', ChoiceType::class, [
                'choices' => $matchRepo->getFormattedDatesForFutureMatches()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
           'data_class' => Pick::class
        ]);

        $resolver->setRequired('match_repository');
    }

    public function getName()
    {
        return 'pick_type';
    }
}
