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
 * Class AssertGlobalSearchOrderID
 * Assert that order ID is present in search results
 */
class AssertGlobalSearchOrderID extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that order ID is present in search results
     *
     * @param Dashboard $dashboard
     * @param OrderInjectable $order
     * @return void
     */
    public function processAssert(Dashboard $dashboard, OrderInjectable $order)
    {
        $orderId = "Order #" . $order->getId();
        $isVisibleInResult = $dashboard->getAdminPanelHeader()->isSearchResultVisible($orderId);
        \PHPUnit_Framework_Assert::assertTrue(
            $isVisibleInResult,
            'Order ID ' . $order->getId() . ' is absent in search results'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Order ID is present in search results';
    }
}
