<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\SalesRule\Test\Page\Adminhtml\PromoQuoteIndex;

/**
 * Class AssertCartPriceRuleSuccessDeleteMessage
 */
class AssertCartPriceRuleSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE = 'The rule has been deleted.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success message is displayed after sales rule delete
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @return void
     */
    public function processAssert(PromoQuoteIndex $promoQuoteIndex)
    {
        $actualMessage = $promoQuoteIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Sales rule success delete message is present.';
    }
}
