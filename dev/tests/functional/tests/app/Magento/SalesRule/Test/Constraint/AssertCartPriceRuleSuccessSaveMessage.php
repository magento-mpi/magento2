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
 * Class AssertCartPriceRuleSuccessSaveMessage
 */
class AssertCartPriceRuleSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The rule has been saved.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after sales rule save
     *
     * @param PromoQuoteIndex $promoQuoteIndex
     * @return void
     */
    public function processAssert(PromoQuoteIndex $promoQuoteIndex)
    {
        $actualMessage = $promoQuoteIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
                . "\nExpected: " . self::SUCCESS_MESSAGE
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
        return 'Sales rule success save message is present.';
    }
}
