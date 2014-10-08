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
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\ObjectManager;

/**
 * Class AssertOrderInOrdersGridOnFrontend
 * Assert that order is present in Orders grid on frontend
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
     * Assert that order is present in Orders grid on frontend
     *
     * @param OrderInjectable $order
     * @param CustomerInjectable $customer
     * @param ObjectManager $objectManager
     * @param CustomerAccountIndex $customerAccountIndex
     * @param OrderHistory $orderHistory
     * @param string $orderStatus
     * @param string $orderId
     * @param string|null $statusToCheck
     * @return void
     */
    public function processAssert(
        OrderInjectable $order,
        CustomerInjectable $customer,
        ObjectManager $objectManager,
        CustomerAccountIndex $customerAccountIndex,
        OrderHistory $orderHistory,
        $orderStatus,
        $orderId = '',
        $statusToCheck = null
    ) {
        $filter = [
            'id' => $order->hasData('id') ? $order->getId() : $orderId,
            'status' => $statusToCheck === null ? $orderStatus : $statusToCheck
        ];
        $customerLogin = $objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $customerLogin->run();
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
