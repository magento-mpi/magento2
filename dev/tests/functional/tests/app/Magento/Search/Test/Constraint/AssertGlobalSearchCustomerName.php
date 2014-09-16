<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Test\Constraint;

use Magento\Backend\Test\Page\Adminhtml\Dashboard;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGlobalSearchCustomerName
 * Assert that customer name is present in search results
 */
class AssertGlobalSearchCustomerName extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that customer name is present in search results
     *
     * @param Dashboard $dashboard
     * @param OrderInjectable $order
     * @return void
     */
    public function processAssert(Dashboard $dashboard, OrderInjectable $order)
    {
        /** @var \Magento\Customer\Test\Fixture\CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();
        $customerName = $customer->getFirstname() . " " . $customer->getLastname();
        $isVisibleInResult = $dashboard->getAdminPanelHeader()->isSearchResultVisible($customerName);
        \PHPUnit_Framework_Assert::assertTrue(
            $isVisibleInResult,
            'Customer name ' . $customerName . ' is absent in search results'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer name is present in search results';
    }
}
