<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Constraint;

use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderView;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertBackendOrdersGiftMessage
 * Assert that message from dataSet is displayed on order(s) view page on backend.
 */
class AssertBackendOrdersGiftMessage extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Skipped fields for verify data.
     *
     * @var array
     */
    protected $skippedFields = [
        'allow_gift_options_for_items',
        'allow_gift_messages_for_order',
        'allow_gift_messages_for_order',
        'allow_gift_options',
    ];

    /**
     * Assert that message from dataSet is displayed on order(s) view page on backend.
     *
     * @param GiftMessage $giftMessage
     * @param OrderView $orderView
     * @param OrderIndex $orderIndex
     * @param array $products
     * @param string $orderId
     * @return void
     */
    public function processAssert(
        GiftMessage $giftMessage,
        OrderView $orderView,
        OrderIndex $orderIndex,
        array $products,
        $orderId
    ) {
        $orderIndex->open()->getSalesOrderGrid()->searchAndOpen(['id' => $orderId]);

        $expectedData = $giftMessage->getData();
        if ($giftMessage->getAllowGiftMessagesForOrder()) {
            $actualData = $orderView->getGiftOptionsBlock()->getData($giftMessage);
            $this->verifyForm($expectedData, $actualData);
        }

        if ($giftMessage->getAllowGiftOptionsForItems()) {
            foreach ($products as $product) {
                $actualData = $orderView->getGiftItemsBlock()->getItemProduct($product)
                    ->getGiftMessageFormData($giftMessage);
                $this->verifyForm($expectedData, $actualData);
            }
        }
    }

    /**
     * Verify form.
     *
     * @param array $expected
     * @param array $actual
     * @return void
     */
    protected function verifyForm($expected, $actual)
    {
        $errors = $this->verifyData($expected, $actual);
        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Return string representation of object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Backend gift message form data is equal to data passed from dataSet.';
    }
}
