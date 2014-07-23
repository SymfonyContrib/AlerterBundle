<?php
namespace SymfonyContrib\Bundle\AlerterBundle\Entity;

use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use SymfonyContrib\Bundle\CronBundle\Entity\Cron;
use SymfonyContrib\Bundle\AlerterBundle\ExpressionLanguage\ExpressionLanguage;

/**
 * Alert entity.
 */
class Alert
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $expression;

    /** @var string */
    protected $parsedExpression;

    /** @var string */
    protected $alerter;

    /** @var string */
    protected $level;

    /** @var string */
    protected $message;

    /** @var string */
    protected $testInterval;

    /** @var boolean */
    protected $enabled;

    /** @var Cron */
    protected $cron;

    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;


    public function __construct()
    {
        $this->enabled = true;
        $this->created = new \DateTime();

        $cron = new Cron();
        $cron->setOwner('SymfonyContrib:AlerterBundle');
        $cron->setDesc('Evaluates an expression and alerts on true.');
        $cron->setGroup('Alerter');
        $this->cron = $cron;
    }

    /**
     * Doctrine lifecycle callback.
     *
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $this->updateCron();
    }

    /**
     * Doctrine lifecycle callback.
     *
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->updateCron();
        $this->parseExpression();
    }

    /**
     * Doctrine lifecycle callback.
     *
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        if (!$args->hasChangedField('updated')) {
            $this->updated = new \DateTime();
        }

        if ($args->hasChangedField('expression')) {
            $this->parseExpression();
        }
    }

    /**
     * Update the associated cron entry.
     */
    public function updateCron()
    {
        $cron = $this->getCron();
        $cron->setName('alerter-' . $this->getId());
        $cron->setJob('alerter.manager:evaluateCron');
        $cron->setRunInterval($this->getTestInterval());
        $cron->setEnabled($this->isEnabled());
    }

    /**
     * Parse expression to save time during execution.
     */
    public function parseExpression()
    {
        $language   = new ExpressionLanguage();
        $names      = ['container'];
        $expression = serialize($language->parse($this->expression, $names)->getNodes());
        $this->setParsedExpression($expression);
    }

    /**
     * Convert object to representative string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }

    /**
     * @param string $alerter
     *
     * @return Alert
     */
    public function setAlerter($alerter)
    {
        $this->alerter = $alerter;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlerter()
    {
        return $this->alerter;
    }

    /**
     * Build a label for this alert.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->getExpression() . '--' . $this->getAlerter() . '--' . $this->getLevel();
    }

    /**
     * @param \DateTime $created
     *
     * @return Alert
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param string $expression
     *
     * @return Alert
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param string $parsedExpression
     *
     * @return Alert
     */
    public function setParsedExpression($parsedExpression)
    {
        $this->parsedExpression = $parsedExpression;

        return $this;
    }

    /**
     * @return string
     */
    public function getParsedExpression()
    {
        return $this->parsedExpression;
    }

    /**
     * @param boolean $enabled
     *
     * @return Alert
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param int $id
     *
     * @return Alert
     */
    public function setId($id)
    {
        $this->id = (int)$id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $level
     *
     * @return Alert
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param string $message
     *
     * @return Alert
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $testInterval
     *
     * @return Alert
     */
    public function setTestInterval($testInterval)
    {
        $this->testInterval = $testInterval;

        return $this;
    }

    /**
     * @return string
     */
    public function getTestInterval()
    {
        return $this->testInterval;
    }

    /**
     * @param \DateTime $updated
     *
     * @return Alert
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param Cron $cron
     *
     * @return Alert
     */
    public function setCron(Cron $cron)
    {
        $this->cron = $cron;

        return $this;
    }

    /**
     * @return Cron
     */
    public function getCron()
    {
        return $this->cron;
    }
}
