<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Page\Adminhtml\RmaIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Assert success message appears after submitting new return request.
 */
class AssertRmaSuccessSaveMessage extends AbstractConstraint
{
    /**
     * Rma success save message.
     */
    const SUCCESS_SAVE_MESSAGE = 'You submitted the RMA request.';

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert success message appears after submitting new return request.
     *
     * @param RmaIndex $rmaIndex
     * @return void
     */
    public function processAssert(RmaIndex $rmaIndex)
    {
        $pageMessage = $rmaIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $pageMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $pageMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message appears after submitting new return request.';
    }
}
