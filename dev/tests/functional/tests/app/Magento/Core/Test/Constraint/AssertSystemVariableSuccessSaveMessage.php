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
 * Class AssertSystemVariableSuccessSaveMessage
 * Check success delete message is correct after Custom System Variable deleted
 */
class AssertSystemVariableSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'You saved the custom variable.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

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
