<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Class AssertCustomerInGrid
 *
 */
class AssertCustomerInGrid extends AbstractConstraint
{
    /* tags */
     const SEVERITY = 'middle';
     /* end tags */

    /**
     * Assert customer availability in Customer Grid
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndex $pageCustomerIndex
     * @param CustomerInjectable $initialCustomer [optional]
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerIndex $pageCustomerIndex,
        CustomerInjectable $initialCustomer = null
    ) {
        if ($initialCustomer) {
            $customer = $customer->hasData()
                ? array_merge($initialCustomer->getData(), $customer->getData())
                : $initialCustomer->getData();
        } else {
            $customer = $customer->getData();
        }
        $name = (isset($customer['prefix']) ? $customer['prefix'] . ' ' : '')
            . $customer['firstname']
            . (isset($customer['middlename']) ? ' ' . $customer['middlename'] : '')
            . ' ' . $customer['lastname']
            . (isset($customer['suffix']) ? ' ' . $customer['suffix'] : '');
        $filter = [
            'name' => $name,
            'email' => $customer['email'],
        ];

        $pageCustomerIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $pageCustomerIndex->getCustomerGridBlock()->isRowVisible($filter),
            'Customer with '
            . 'name \'' . $filter['name'] . '\', '
            . 'email \'' . $filter['email'] . '\' '
            . 'is absent in Customer grid.'
        );
    }

    /**
     * Text success exist Customer in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer is present in Customer grid.';
    }
}
