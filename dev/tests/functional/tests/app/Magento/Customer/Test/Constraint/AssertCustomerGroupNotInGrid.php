<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerGroup;
use Magento\Customer\Test\Page\Adminhtml\CustomerGroupIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerGroupNotInGrid
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerGroupNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that customer group not in grid
     *
     * @param CustomerGroup $customerGroup
     * @param CustomerGroupIndex $customerGroupIndex
     * @return void
     */
    public function processAssert(
        CustomerGroup $customerGroup,
        CustomerGroupIndex $customerGroupIndex
    ) {
        $customerGroupIndex->open();
        $filter = ['type' => $customerGroup->getCode()];
        \PHPUnit_Framework_Assert::assertFalse(
            $customerGroupIndex->getCustomerGroupGrid()->isRowVisible($filter),
            'Group with name \'' . $customerGroup->getCode() . '\' in customer groups grid.'
        );
    }

    /**
     * Text of customer group not in grid.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group not in grid.';
    }
}
