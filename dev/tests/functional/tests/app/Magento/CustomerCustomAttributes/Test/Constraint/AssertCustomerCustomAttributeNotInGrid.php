<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\CustomerCustomAttributes\Test\Page\Adminhtml\CustomerAttributeIndex;

/**
 * Class AssertCustomerCustomAttributeNotInGrid
 * Assert that deleted customer attribute cannot be found in grid
 */
class AssertCustomerCustomAttributeNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted customer attribute cannot be found in grid
     *
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerAttributeIndex $customerAttributeIndex
     * @return void
     */
    public function processAssert(
        CustomerCustomAttribute $customerAttribute,
        CustomerAttributeIndex $customerAttributeIndex
    ) {
        $data = $customerAttribute->getData();
        $filter = [
            'attribute_code' => $data['attribute_code'],
            'frontend_label' => $data['frontend_label']
        ];
        $customerAttributeIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $customerAttributeIndex->getCustomerCustomAttributesGrid()->isRowVisible($filter, true, false),
            "Customer Attribute with code '" . $filter['attribute_code'] . "' is present in Customer Attributes grid."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is absent in grid.';
    }
}
