<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Assert that gift card account in grid.
 */
class AssertGiftCardAccountInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account in grid.
     *
     * @param GiftCardAccount $giftCardAccount
     * @param Index $index
     * @return void
     */
    public function processAssert(GiftCardAccount $giftCardAccount, Index $index)
    {
        $index->open();
        if ($giftCardAccount->hasData('date_expires')) {
            $dateExpires = strftime("%b %e, %Y", strtotime($giftCardAccount->getDateExpires()));
        } else {
            $dateExpires = '--';
        }
        $balance = $giftCardAccount->getBalance();
        $filter = [
            'balance' => $balance,
            'date_expires' => $dateExpires,
        ];
        \PHPUnit_Framework_Assert::assertTrue(
            $index->getGiftCardAccount()->isRowVisible($filter, false),
            "Gift card with balance = '$balance' and date expires = '$dateExpires' is absent in gift card account grid."
        );
    }

    /**
     * Success assert of  gift card account in grid.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer gift card account in grid.';
    }
}
