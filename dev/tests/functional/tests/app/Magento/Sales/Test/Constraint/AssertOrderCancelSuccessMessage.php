<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\Adminhtml\OrderView;

/**
 * Class AssertOrderCancelSuccessMessage
 * Assert that after cancel sales order successful message appears
 */
class AssertOrderCancelSuccessMessage extends AbstractConstraint
{
    /**
     * Message displayed after cancel sales order
     */
    const SUCCESS_CANCEL_MESSAGE = 'You canceled the order.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after cancel sales order successful message appears
     *
     * @param OrderView $orderView
     * @return void
     */
    public function processAssert(OrderView $orderView)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_CANCEL_MESSAGE,
            $orderView->getMessagesBlock()->getSuccessMessages()
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order success cancel message is present.';
    }
}
