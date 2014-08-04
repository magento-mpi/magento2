<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

/**
 * Class AssertProductSimpleDuplicateForm
 * Assert form data equals duplicate simple product data
 */
class AssertProductSimpleDuplicateForm extends AssertProductDuplicateForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';
}
