<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGroupedPriceOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertGroupedPriceOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @return void
     */
    public function processAssert()
    {
        // TODO Implemented in MTA-15
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
