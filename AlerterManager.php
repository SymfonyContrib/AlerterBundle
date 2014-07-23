<?php

namespace SymfonyContrib\Bundle\AlerterBundle;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\EntityManager;
use SymfonyContrib\Bundle\CronBundle\Entity\Cron;
use SymfonyContrib\Bundle\AlerterBundle\Entity\Alert;
use SymfonyContrib\Bundle\AlerterBundle\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SerializedParsedExpression;

/**
 * Execute alert tests.
 */
class AlerterManager extends ContainerAware
{
    /** @var array */
    public $alerters;

    public function value()
    {
        return 4;
    }

    public function compare()
    {
        return 2;
    }

    public function __construct(array $alerters = [])
    {
        $this->alerters = $alerters;
    }

    public function evaluateCron(Cron $cron)
    {
        $alert = $this->container->get('doctrine')
            ->getRepository('AlerterBundle:Alert')
            ->findOneBy(['cron' => $cron]);

        if (!$alert) {
            throw new \InvalidArgumentException('Alert not found.');
        }

        $this->evaluateAlert($alert);
    }

    public function evaluateAlert(Alert $alert)
    {
        // Evaluate expression and alert if true.
        if ($this->evaluateExpression($alert->getExpression(), $alert->getParsedExpression())) {
            $alerters    = $this->container->getParameter('alerter.alerters');
            $alerterName = $alert->getAlerter();
            if (empty($alerters[$alerterName])) {
                throw new \InvalidArgumentException('Alerter not found.');
            }
            $alerter = $this->container->get($alerters[$alerterName]);
            $alerter->{$alert->getLevel()}($alert->getMessage());
        }
    }

    public function evaluateExpression($expression, $serializedParsedExpression = null)
    {
        $language = new ExpressionLanguage();
        if ($serializedParsedExpression) {
            $expression = new SerializedParsedExpression($expression, $serializedParsedExpression);
        }

        return $language->evaluate($expression, ['container' => $this->container]);
    }
}
