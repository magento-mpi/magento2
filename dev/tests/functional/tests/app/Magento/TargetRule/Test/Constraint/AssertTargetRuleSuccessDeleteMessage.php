<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\TargetRule\Test\Constraint;

use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTargetRuleSuccessDeleteMessage
 */
class AssertTargetRuleSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    const SUCCESS_DELETE_MESSAGE = 'You deleted the rule.';

    /**
     * Assert that success message is displayed after target rule delete
     *
     * @param TargetRuleIndex $targetRuleIndex
     * @return void
     */
    public function processAssert(TargetRuleIndex $targetRuleIndex)
    {
        $actualMessage = $targetRuleIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text success delete message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Success message about rule deleting is present.';
    }
}
