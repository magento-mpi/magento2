<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Tax\Test\Page\Adminhtml\TaxRateIndex;

/**
 * Class AssertTaxRateSuccessDeleteMessage
 */
class AssertTaxRateSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = 'The tax rate has been deleted.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that success delete message is displayed after tax rate deleted
     *
     * @param TaxRateIndex $taxRateIndex
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndex)
    {
        $actualMessage = $taxRateIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success delete message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of Deleted Tax Rate Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate success delete message is present.';
    }
}
