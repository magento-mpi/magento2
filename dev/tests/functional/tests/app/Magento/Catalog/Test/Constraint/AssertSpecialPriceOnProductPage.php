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
 * Class AssertSpecialPriceOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertSpecialPriceOnProductPage extends AbstractConstraint
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
        // Implemented in MTA-15
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
