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
 * Class AssertProductAttributeIsGlobal
 *
 * @package Magento\Catalog\Test\Constraint
 */
class AssertProductAttributeIsGlobal extends AbstractConstraint
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
