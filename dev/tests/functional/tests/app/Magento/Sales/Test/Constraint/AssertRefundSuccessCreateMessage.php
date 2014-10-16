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
 * Class AssertRefundSuccessCreateMessage
 * Assert that success create credit memo message is present on order view page
 */
class AssertRefundSuccessCreateMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const SUCCESS_CREATE_MESSAGE = 'You created the credit memo.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success create credit memo message is present on order view page
     *
     * @param OrderView $orderView
     * @return void
     */
    public function processAssert(OrderView $orderView)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_CREATE_MESSAGE,
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
        return 'Success create credit memo message is present  on order view page.';
    }
}
