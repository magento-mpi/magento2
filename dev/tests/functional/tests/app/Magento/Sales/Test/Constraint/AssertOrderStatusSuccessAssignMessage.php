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
 * Class AssertOrderStatusSuccessAssignMessage
 * Assert that after assigning order status success message appears
 */
class AssertOrderStatusSuccessAssignMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You have assigned the order status.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after order status assigning.
     *
     * @param OrderStatusIndex $orderStatusIndexPage
     * @return void
     */
    public function processAssert(OrderStatusIndex $orderStatusIndexPage)
    {
        $actualMessage = $orderStatusIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(self::SUCCESS_MESSAGE, $actualMessage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Order status success assign message is present.';
    }
}
