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
 * Class AssertProductAttributeOnProductForm
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductAttributeOnProductForm extends AbstractConstraint
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
    }

    /**
     * @return string
     */
    public function toString()
    {
        //
    }
}
