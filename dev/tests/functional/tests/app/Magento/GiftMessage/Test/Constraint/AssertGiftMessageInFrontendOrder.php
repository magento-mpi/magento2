<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Page\OrderView;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftMessageInFrontendOrder
 * Assert that message from dataSet is displayed on order(s) view page on frontend
 */
class AssertGiftMessageInFrontendOrder extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that message from dataSet is displayed on order(s) view page on frontend
     *
     * @param GiftMessage $giftMessage
     * @param CustomerInjectable $customer
     * @param OrderHistory $orderHistory
     * @param OrderView $orderView
     * @param string $orderId
     * @return void
     */
    public function processAssert(
        GiftMessage $giftMessage,
        CustomerInjectable $customer,
        OrderHistory $orderHistory,
        OrderView $orderView,
        $orderId
    ) {
        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();

        $expectedData = [
            'sender' => $giftMessage->getSender(),
            'recipient' => $giftMessage->getRecipient(),
            'message' => $giftMessage->getMessage(),
        ];
        $orderHistory->open();
        $orderHistory->getOrderHistoryBlock()->openOrderById($orderId);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedData,
            $orderView->getGiftMessageForOrderBlock()->getGiftMessage(),
            'Wrong gift message is displayed on order.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return "Gift message is displayed on order(s) view page on frontend correctly.";
    }
}
