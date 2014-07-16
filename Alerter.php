<?php

namespace SymfonyContrib\Bundle\AlerterBundle;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\EntityManager;
use SymfonyContrib\Bundle\CronBundle\Entity\Cron;
use SymfonyContrib\Bundle\AlerterBundle\Entity\Alert;

/**
 * Execute alert tests.
 */
class Alerter extends ContainerAware
{

    public function test()
    {
        return false;
    }

    public function __call($method, $args)
    {
        if (strpos($method, 'datapoint_') !== 0 || empty($args[0]) || !$args[0] instanceof Cron) {
            return;
        }
        $cron = $args[0];

        $alert = $this->container->get('doctrine')
            ->getRepository('AlerterBundle:Alert')
            ->findOneBy(['cron' => $cron]);

        if (!$alert) {
            throw new \InvalidArgumentException('Alert not found.');
        }

        $this->execute($alert);
    }

    protected function execute(Alert $alert)
    {
        $alerters   = $this->container->getParameter('alerter.alerters');
        $dataPoints = $this->container->getParameter('alerter.data_points');

        $alerterName = $alert->getAlerter();
        if (empty($alerters[$alerterName])) {
            throw new \InvalidArgumentException('Alerter not found.');
        }
        $alerter = $alerters[$alerterName];

        $dataPointName = $alert->getDataPoint();
        if (empty($dataPoints[$dataPointName])) {
            throw new \InvalidArgumentException('Alerter data point not found.');
        }
        $dataPoint = $dataPoints[$dataPointName];

        $service = $this->container->get($dataPoint['service']);
        $actual  = $service->{$dataPoint['method']}();

        if ($this->testDataPoint($actual, $alert->getOperator(), $alert->getValue())) {
            $alerter = $this->container->get($alerter);
            $alerter->{$alert->getLevel()}($alert->getMessage(), ['actual' => $actual]);
        }
    }

    /**
     * Test a value.
     *
     * @param $actual
     * @param $operator
     * @param $testValue
     *
     * @return bool
     */
    protected function testDataPoint($actual, $operator, $testValue)
    {
        switch ($operator) {
            case 'is true':
                return $actual === true;

            case 'is false':
                return $actual === false;

            case 'not true':
                return $actual !== true;

            case 'not false':
                return $actual !== false;

            case '=':
                return ((string)$actual) === $testValue;

            case '!=':
                return ((string)$actual) !== $testValue;

            case '>':
                return $actual > $testValue;

            case '>=':
                return $actual >= $testValue;

            case '<':
                return $actual < $testValue;

            case '<=':
                return $actual <= $testValue;
        }

        return false;
    }
}
