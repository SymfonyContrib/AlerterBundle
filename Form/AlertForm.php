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

    public function __construct(array $alerters = [])
    {
        $this->alerters   = $alerters;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('expression', 'text', [

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
