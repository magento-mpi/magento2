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
 * Class AssertOrderOnHoldFailMessage
 * Assert on hold fail message is displayed on order index page
 */
class AssertOrderOnHoldFailMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const FAIL_ON_HOLD_MESSAGE = 'No order(s) were put on hold.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert on hold fail message is displayed on order index page
     *
     * @param OrderIndex $orderIndex
     * @return void
     */
    public function processAssert(OrderIndex $orderIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::FAIL_ON_HOLD_MESSAGE,
            $orderIndex->getMessagesBlock()->getErrorMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'On hold fail message is displayed on order index page.';
    }
}
