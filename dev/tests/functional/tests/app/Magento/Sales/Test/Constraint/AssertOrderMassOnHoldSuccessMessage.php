<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;

/**
 * Class AssertOrderMassOnHoldSuccessMessage
 * Assert mass on hold success message is displayed on order index page
 */
class AssertOrderMassOnHoldSuccessMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_ON_HOLD_MESSAGE = 'You have put %d order(s) on hold.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert on hold success message is displayed on order index page
     *
     * @param OrderIndex $orderIndex
     * @param int $ordersCount
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex, $ordersCount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::SUCCESS_ON_HOLD_MESSAGE, $ordersCount),
            $orderIndex->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'On hold success message is displayed on order index page.';
    }
}
