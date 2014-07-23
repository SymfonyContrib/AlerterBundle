<?php

namespace SymfonyContrib\Bundle\AlerterBundle\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage as BaseExpressionLanguage;

/**
 * Adds some functions to the default ExpressionLanguage.
 *
 * To get a service, use service('request').
 * To get a parameter, use parameter('kernel.debug').
 */
class ExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register('service', function ($arg) {
            return sprintf('$this->get(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->get($value);
        });

        $this->register('parameter', function ($arg) {
            return sprintf('$this->getParameter(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->getParameter($value);
        });
    }
}
