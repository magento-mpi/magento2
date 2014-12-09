<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;

/**
 * Class AssertTargetRuleSuccessSaveMessage
 */
class AssertTargetRuleSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the rule.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that success message is displayed after target rule save
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @return void
     */
    public function processAssert(TargetRuleIndex $targetRuleIndex)
    {
        $actualMessages = $targetRuleIndex->getMessagesBlock()->getSuccessMessages();
        if (!is_array($actualMessages)) {
            $actualMessages = [$actualMessages];
        }
        \PHPUnit_Framework_Assert::assertContains(
            self::SUCCESS_MESSAGE,
            $actualMessages,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . implode(',', $actualMessages)
        );
    }

    /**
     * Text success save message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Target rule success save message is present.';
    }
}
