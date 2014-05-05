<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Magento\Tax\Test\Page\Adminhtml\TaxRuleIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertSuccessSavedMessageTaxRule
 *
 * @package Magento\Tax\Test\Constraint
 */
class AssertSuccessSavedMessageTaxRule extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The tax rule has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after tax rule saved
     *
     * @param TaxRuleIndex $taxRuleIndex
     */
    public function processAssert(TaxRuleIndex $taxRuleIndex)
    {
        $actualMessage = $taxRuleIndex->getMessageBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of Created Tax Rule Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rule success create message is present.';
    }
}
