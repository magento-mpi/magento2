<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Constraint;

use Magento\CurrencySymbol\Test\Page\Adminhtml\SystemCurrencySymbolIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCurrencySymbolSuccessSaveMessage
 */
class AssertCurrencySymbolSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_SAVE_MESSAGE = 'The custom currency symbols were applied.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after clicking on 'Save Currency Symbols' button success message appears.
     *
     * @param SystemCurrencySymbolIndex $currencySymbolIndex
     * @return void
     */
    public function processAssert(SystemCurrencySymbolIndex $currencySymbolIndex)
    {
        $actualMessage = $currencySymbolIndex->getMessagesBlock()->getSuccessMessages();
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
        return 'Currency Symbol success save message is correct.';
    }
}
