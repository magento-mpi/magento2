<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint; 

use Magento\Sales\Test\Page\Adminhtml\OrderStatusNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertDuplicateStatus
 *
 * @package Magento\Sales\Test\Constraint
 */
class AssertDuplicateStatus extends AbstractConstraint
{
    const DUPLICATE_MESSAGE = 'We found another order status with the same order status code.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * @return void
     */
    public function processAssert(
        OrderStatusNew $orderStatusNewPage
    ) {
        $actualMessage = $orderStatusNewPage->getMessageBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DUPLICATE_MESSAGE,
            $actualMessage,
            'Wrong duplicate message is displayed.'
            . "\nExpected: " . self::DUPLICATE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Order status duplicate message is present.';
    }
}
