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
 * Class AssertGiftMessageIsInOrder
 * Assert that message from dataSet is displayed on order(s) view page on frontend
 */
class AssertGiftMessageIsInOrder extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that message from dataSet is displayed on order(s) view page on frontend
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

        if ($giftMessage->getAllowGiftMessagesForOrder() === 'Yes') {
            \PHPUnit_Framework_Assert::assertEquals(
                $expectedData,
                $orderView->getGiftMessageForOrderBlock()->getGiftMessage(),
                'Wrong gift message is displayed on order.'
            );
        }
        if ($giftMessage->getAllowGiftOptionsForItems() === 'Yes') {
            foreach ($products as $product) {
                \PHPUnit_Framework_Assert::assertEquals(
                    $expectedData,
                    $orderView->getGiftMessageForItemBlock()->getGiftMessage($product->getName()),
                    'Wrong gift message is displayed on item.'
                );
            }
        }
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
