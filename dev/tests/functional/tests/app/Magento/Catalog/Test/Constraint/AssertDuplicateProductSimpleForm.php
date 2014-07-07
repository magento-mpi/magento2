<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

/**
 * Class AssertDuplicateProductSimpleForm
 * Assert form data equals duplicate simple product data
 */
class AssertDuplicateProductSimpleForm extends AssertDuplicateProductForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';
}
