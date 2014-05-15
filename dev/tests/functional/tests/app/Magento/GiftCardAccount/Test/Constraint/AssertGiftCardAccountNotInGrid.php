<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;

/**
 * Class AssertGiftCardAccounNotInGrid
 * Assert that gift card account not in grid
 */
class AssertGiftCardAccountNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account not in grid
     *
     * @param GiftCardAccount $giftCardAccount
     * @param Index $index
     * @return void
     */
    public function processAssert(
        GiftCardAccount $giftCardAccount,
        Index $index
    ) {
        $index->open();
        if ($giftCardAccount->hasData('date_expires')) {
            $dateExpires = strftime("%b %#d, %Y", strtotime($giftCardAccount->getDateExpires()));
        } else {
            $dateExpires = '--';
        }
        $filter = [
            'balance' => $giftCardAccount->getBalance(),
            'state' => 'Available',
            'date_expires' => $dateExpires,
        ];
        \PHPUnit_Framework_Assert::assertFalse(
            $index->getGiftCardAccount()->isRowVisible($filter, false),
            'Gift card is present in gift card account grid.'
        );
    }

    /**
     * Success assert that gift card account not in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account not in grid.';
    }
}
