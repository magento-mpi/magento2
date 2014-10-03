<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Constraint;

use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Magento\Sales\Test\Page\OrderHistory;
use Magento\Sales\Test\Page\OrderView;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftMessageInFrontendOrderItems
 * Assert that message from dataSet is displayed for each items on order(s) view page on frontend
 */
class AssertGiftMessageInFrontendOrderItems extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that message from dataSet is displayed for each items on order(s) view page on frontend
     *
     * @param GiftMessage $giftMessage
     * @param OrderHistory $orderHistory
     * @param OrderView $orderView
     * @param string $orderId
     * @param array $products
     * @return void
     */
    public function processAssert(
        GiftMessage $giftMessage,
        OrderHistory $orderHistory,
        OrderView $orderView,
        $orderId,
        $products = []
    ) {
        $expectedData = [
            'sender' => $giftMessage->getSender(),
            'recipient' => $giftMessage->getRecipient(),
            'message' => $giftMessage->getMessage(),
        ];
        $orderHistory->open();
        $orderHistory->getOrderHistoryBlock()->openOrderById($orderId);

        foreach ($products as $product) {
            \PHPUnit_Framework_Assert::assertEquals(
                $expectedData,
                $orderView->getGiftMessageForItemBlock()->getGiftMessage($product->getName()),
                'Wrong gift message is displayed on "' . $product->getName() . '" item.'
            );
        }
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return "Gift message is displayed for each items on order(s) view page on frontend correctly.";
    }
}
