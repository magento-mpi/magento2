<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardAccounNotInGrid
 * Assert that gift card account not in grid
 */
class AssertGiftCardAccountNotInGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that gift card account not in grid
     *
     * @param GiftCardAccount $giftCardAccount
     * @param Index $index
     * @return void
     */
    public function processAssert(GiftCardAccount $giftCardAccount, Index $index)
    {
        $index->open();
        if ($giftCardAccount->hasData('date_expires')) {
            $dateExpires = strftime("%b %#d, %Y", strtotime($giftCardAccount->getDateExpires()));
        } else {
            $dateExpires = '--';
        }
        $filter = [
            'balance' => $giftCardAccount->getBalance(),
            'date_expires' => $dateExpires,
        ];
        $index->getGiftCardAccount()->sortGridByField('giftcardaccount_id');
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
        return 'Gift card account is absent in grid.';
    }
}
