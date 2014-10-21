<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Rma\Test\Page\Adminhtml\RmaIndex;

/**
 * Class AssertRmaSuccessSaveMessage
 * Assert success message appears after submitting new return request.
 */
class AssertRmaSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'You submitted the RMA request.';

    /**
     * Constraint severeness
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
