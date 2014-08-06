<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertConfigurableProductPage
 */
class AssertConfigurableProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * @return void
     */
    public function processAssert()
    {
        $d = 1;
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}

