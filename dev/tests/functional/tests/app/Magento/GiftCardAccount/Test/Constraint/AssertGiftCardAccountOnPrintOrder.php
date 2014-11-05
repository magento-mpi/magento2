<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestPrint;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Assert that gift card amount printed correctly on sales guest print page.
 */
class AssertGiftCardAccountOnPrintOrder extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card amount printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, GiftCardAccount $giftCardAccount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            number_format($giftCardAccount->getBalance(), 2),
            $salesGuestPrint->getPrintOrderGiftCard()->getGiftCardsBlock()->getGiftCardDiscount()
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Gift card amount was printed correctly.";
    }
}
