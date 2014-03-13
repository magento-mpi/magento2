<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\App\State;

/**
 * Class State2
 * Example Application State class
 *
 * @package Mtf\App\State
 */
class State2 extends AbstractState
{
    /**
     * @inheritdoc
     */
    protected $isCleanInstance = true;

    /**
     * @inheritdoc
     */
    public function apply()
    {
        parent::apply();
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Configuration Profile #2';
    }
}
