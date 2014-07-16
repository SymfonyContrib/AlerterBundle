<?php

namespace SymfonyContrib\Bundle\AlerterBundle\Form;

use SymfonyContrib\Bundle\AlerterBundle\Entity\Alert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Alert admin add/edit form.
 */
class AlertForm extends AbstractType
{
    /** @var array */
    public $alerters;

    /** @var array */
    public $dataPoints;

    public function __construct(array $alerters = [], array $dataPoints = [])
    {
        $this->alerters   = $alerters;
        $this->dataPoints = $dataPoints;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataPoint', 'choice', [
                'choices' => array_combine(array_keys($this->dataPoints), array_keys($this->dataPoints)),
            ])
            ->add('operator', 'choice', [
                'choices' => [
                    '='         => '=',
                    '!='        => '!=',
                    '>'         => '>',
                    '>='        => '>=',
                    '<'         => '<',
                    '<='        => '<=',
                    'is true'   => 'is true',
                    'is false'  => 'is false',
                    'not true'  => 'not true',
                    'not false' => 'not false',
                ],
            ])
            ->add('value', 'text', [
                'required' => false,
            ])
            ->add('alerter', 'choice', [
                'choices' => array_combine(array_keys($this->alerters), array_keys($this->alerters)),
            ])
            ->add('level', 'choice', [
                'choices' => [
                    'debug'     => 'debug',
                    'info'      => 'info',
                    'notice'    => 'notice',
                    'warning'   => 'warning',
                    'error'     => 'error',
                    'critical'  => 'critical',
                    'alert'     => 'alert',
                    'emergency' => 'emergency',
                ],
            ])
            ->add('message', 'text')
            ->add('testInterval', 'choice', [
                // @todo Make this configurable.
                'choices' => [
                    '*/15 * * * *' => '15 minutes',
                    '*/30 * * * *' => '30 minutes',
                    '*/45 * * * *' => '45 minutes',
                    '0 * * * *'    => 'hourly',
                    '0 0 * * *'    => 'daily',
                    '0 0 * * 1'    => 'weekly',
                ],
            ])
            ->add('enabled', 'checkbox', [
                'required' => false,
            ])
            ->add('save', 'submit', [
                'attr' => [
                    'class' => 'btn-success',
                ],
            ])
            ->add('cancel', 'button', [
                'url' => $options['cancel_url'],
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'SymfonyContrib\Bundle\AlerterBundle\Entity\Alert',
            'cancel_url' => '/',
        ]);
    }

    public function getName()
    {
        return 'alerter_alert_form';
    }
}
