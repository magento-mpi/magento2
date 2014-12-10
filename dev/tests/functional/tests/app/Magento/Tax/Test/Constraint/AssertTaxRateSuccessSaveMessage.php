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
 * Class AssertTaxRateSuccessSaveMessage
 */
class AssertTaxRateSuccessSaveMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    const SUCCESS_MESSAGE = 'The tax rate has been saved.';

    /**
     * Assert that success message is displayed after tax rate saved
     *
     * @param TaxRateIndex $taxRateIndexPage
     * @return void
     */
    public function processAssert(TaxRateIndex $taxRateIndexPage)
    {
        $actualMessage = $taxRateIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of Created Tax Rate Success Message assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tax rate success create message is present.';
    }
}
