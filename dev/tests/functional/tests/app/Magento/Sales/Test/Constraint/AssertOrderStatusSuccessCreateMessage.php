<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint; 

use Magento\Sales\Test\Page\Adminhtml\OrderStatusIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertOrderStatusSuccessCreateMessage
 *
 * @package Magento\Sales\Test\Constraint
 */
class AssertOrderStatusSuccessCreateMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You have saved the order status.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * @return void
     */
    public function processAssert(
        OrderStatusIndex $orderStatusIndexPage
    ) {
        $actualMessage = $orderStatusIndexPage->getMessageBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Order status success create message is present.';
    }
}
