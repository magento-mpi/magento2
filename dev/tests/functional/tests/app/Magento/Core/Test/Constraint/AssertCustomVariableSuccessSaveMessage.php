<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;

/**
 * Class AssertCustomVariableSuccessSaveMessage
 * Check success delete message is correct after Custom System Variable deleted
 */
class AssertCustomVariableSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_SAVE_MESSAGE = 'You saved the custom variable.';

    /**
     * Assert that success delete message is correct after Custom System Variable deleted
     *
     * @param SystemVariableIndex $systemVariableIndexPage
     * @return void
     */
    public function processAssert(SystemVariableIndex $systemVariableIndexPage)
    {
        $actualMessage = $systemVariableIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_SAVE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_SAVE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom Variable success save message is correct.';
    }
}
