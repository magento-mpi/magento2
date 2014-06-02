<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint; 

use Magento\TargetRule\Test\Page\Adminhtml\TargetRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertTargetRuleSuccessDeleteMessage
 */
class AssertTargetRuleSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You deleted the rule.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

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
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
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
        return 'Target rule success delete message is present.';
    }
}
