<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Sales\Test\Page\SalesOrder;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerCustomAttributeNotOnCreateOrderBackend
 * Assert that created customer attribute is absent during creating order on backend
 */
class AssertCustomerCustomAttributeNotOnCreateOrderBackend extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created customer attribute is absent during creating order on backend
     *
     * @param SalesOrder $salesOrder
     * @param OrderCreateIndex $orderCreateIndex
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        SalesOrder $salesOrder,
        OrderCreateIndex $orderCreateIndex,
        CustomerCustomAttribute $customerAttribute,
        CustomerInjectable $customer
    ) {
        $salesOrder->open();
        $salesOrder->getGridPageActions()->addNew();
        $orderCreateIndex->getCustomerBlock()->selectCustomer($customer);
        $orderCreateIndex->getStoreBlock()->selectStoreView();
        \PHPUnit_Framework_Assert::assertFalse(
            $orderCreateIndex->getCustomerAttributeCreateBlock()->isCustomerAttributeVisible($customerAttribute),
            'Customer Custom Attribute with attribute code: \'' . $customerAttribute->getAttributeCode() . '\' '
            . 'is present during creating order on backend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is absent during creating order on backend.';
    }
}
