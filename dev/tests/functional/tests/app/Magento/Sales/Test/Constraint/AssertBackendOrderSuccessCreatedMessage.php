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
 * Class AssertBackendOrderSuccessCreatedMessage
 * Assert Message presents "You created the order."
 */
class AssertBackendOrderSuccessCreatedMessage extends AbstractConstraint
{
    /**
     * Message displayed after created sales order
     */
    const SUCCESS_MESSAGE = "You created the order.";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that after create sales order successful message appears
     *
     * @param OrderView $orderView
     * @return void
     */
    public function processAssert(OrderView $orderView)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $orderView->getMessagesBlock()->getSuccessMessages(),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales order success created message is present.';
    }
}
