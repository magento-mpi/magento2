<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesOrder;
use Magento\Sales\Test\Page\SalesOrderCreateIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\CustomerCustomAttributes\Test\Fixture\CustomerCustomAttribute;

/**
 * Class AssertCustomerCustomAttributeOnCreateOrderBackend
 * Assert that created customer attribute is available during creating order on backend
 */
class AssertCustomerCustomAttributeOnCreateOrderBackend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created customer attribute is available during creating order on backend
     *
     * @param SalesOrder $salesOrder
     * @param SalesOrderCreateIndex $salesOrderCreateIndex
     * @param CustomerCustomAttribute $customerAttribute
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        SalesOrder $salesOrder,
        SalesOrderCreateIndex $salesOrderCreateIndex,
        CustomerCustomAttribute $customerAttribute,
        CustomerInjectable $customer
    ) {
        $salesOrder->open();
        $salesOrder->getOrderActionsBlock()->add();
//        $filter = [
//            'email' => $customer->getEmail(),
//        ];
        $salesOrderCreateIndex->getCustomerBlock()->selectCustomer($customer);
//        $salesOrderCreateIndex->getCustomerBlock()->searchAndOpen($filter);
//        $salesOrderCreateIndex->getCustomerBlock()->waitForElementNotVisible('//ancestor::body//*/div[@class="loader"]');
//        $salesOrderCreateIndex->getCreateBlock()->waitForElementVisible('form[id="edit_form"]');
        $attributeCode = $customerAttribute->getAttributeCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $salesOrderCreateIndex->getCreateBlock()->isCustomerAttributeVisible($attributeCode),
            'Customer Custom Attribute with attribute code: \'' . $attributeCode . '\' '
            . 'is absent during creating order on backend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Attribute is present during creating order on backend.';
    }
}
