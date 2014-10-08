<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderStatus;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertOrderNotVisibleOnMyAccount
 * Assert order is not visible on frontend in customer account
 */
class AssertOrderNotVisibleOnMyAccount extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert order is not visible on frontend in customer account
     *
     * @param OrderInjectable $order
     * @param CustomerInjectable $customer
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param OrderHistory $orderHistory
     * @param string $orderStatus
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        CustomerInjectable $customer,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        OrderHistory $orderHistory,
        $orderStatus
    ) {
        $filter = [
            'id' => $order->getId(),
            'status' => $orderStatus,
        ];
        $customerAccountLogin->open()->getLoginBlock()->login($customer);
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Orders');
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertFalse(
            $orderHistory->getOrderHistoryBlock()->isOrderVisible($filter),
            'Order with following data \'' . $errorMessage . '\' is present in Orders block on frontend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order absent in orders on frontend.';
    }
}
