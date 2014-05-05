<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardSuccessRedeemedMessage
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardSuccessRedeemedMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'Gift Card "%gift_card_account_code%" was redeemed.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(CustomerAccountIndex $customerAccountIndex)
    {
        $message = $customerAccountIndex->getMessages()->getSuccessMessages();
        $actualMessage = preg_replace('`"(.*?)"`', '"%gift_card_account_code%"', $message);
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text that success message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that success redeemed message is displayed.';
    }
}
