<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Test\Constraint;

use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTargetRuleSuccessSaveMessage
 */
class AssertTargetRuleSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    const SUCCESS_MESSAGE = 'You saved the rule.';

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
