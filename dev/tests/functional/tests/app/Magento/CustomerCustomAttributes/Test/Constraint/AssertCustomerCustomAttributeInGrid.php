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
 * Class AssertCustomerCustomAttributeInGrid
 * Assert that created Customer Attribute can be found in grid
 */
class AssertCustomerCustomAttributeInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created Customer Attribute can be found in grid via:
     * label, required, is_user_defined, visibility, sort order
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
            'frontend_label' => $data['frontend_label'],
            'sort_order' => $data['sort_order'],
        ];

        $filter['is_required'] = isset($data['scope_is_required']) ? $data['scope_is_required'] : null;
        $filter['is_visible'] = isset($data['scope_is_visible']) ? $data['scope_is_visible'] : null;

        \PHPUnit_Framework_Assert::assertTrue(
            $customerAttributeIndex->getCustomerCustomAttributesGrid()->isRowVisible($filter, true, false),
            "Customer Attribute with "
            . "label '" . $filter['frontend_label'] . "', "
            . "sort order '" . $filter['sort_order'] . "', "
            . (isset($filter['is_required'])
                ? ("scope_is_required '" . $filter['is_required'] . "', ")
                : "")
            . (isset($filter['is_visible']) ? ("scope_is_visible '" . $filter['is_visible'] . "' ") : "")
            . "is absent in Customer Attributes grid."
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is present in grid.';
    }
}
