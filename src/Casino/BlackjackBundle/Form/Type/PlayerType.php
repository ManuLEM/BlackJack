<?php

namespace Casino\BlackjackBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlayerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->add('name', 'text', array(
            'attr' => array(
                'placeholder' => 'Name'
            ),
            'label' => false
        ));
    }

    public function getName()
    {
        return 'player';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Casino\BlackjackBundle\Entity\Player',
        ));
    }
}