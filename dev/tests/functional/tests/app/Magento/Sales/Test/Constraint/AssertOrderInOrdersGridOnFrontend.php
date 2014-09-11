<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AssertOrderInOrdersGridOnFrontend
 * Assert that order is present in Orders on frontend
 */
class AssertOrderInOrdersGridOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that order with fixture data is present in MyAccount -> Orders on frontend
     *
     * @param OrderInjectable $order
     * @param CustomerInjectable $customer
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param OrderHistory $orderHistory
     * @param string $status
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        CustomerInjectable $customer,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        OrderHistory $orderHistory,
        $status
    ) {
        $data = $order->getData();
        $filter = [
            'id' => $data['id'],
            'status' => $status,
        ];
        $customerAccountLogin->open()->getLoginBlock()->login($customer);
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Orders');
        $errorMessage = implode(', ', $filter);
        \PHPUnit_Framework_Assert::assertTrue(
            $orderHistory->getOrderHistoryBlock()->isOrderVisible($filter),
            'Order with following data \'' . $errorMessage . '\' is absent in Orders block on frontend.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order is present in orders on frontend.';
    }
}
