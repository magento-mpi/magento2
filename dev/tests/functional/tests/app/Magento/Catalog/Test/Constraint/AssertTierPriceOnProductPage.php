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
 * Class AssertTierPriceOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertTierPriceOnProductPage extends AbstractConstraint
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
        //
        $r="10";
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
