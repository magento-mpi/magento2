<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\GiftCardAccountIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftCardAccountInGrid
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardAccountInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account in grid
     *
     * @param GiftCardAccount $giftCardAccount
     * @param GiftCardAccountIndex $giftCardAccountIndex
     * @return void
     */
    public function processAssert(
        GiftCardAccount $giftCardAccount,
        GiftCardAccountIndex $giftCardAccountIndex
    ) {
        $giftCardAccountIndex->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        \PHPUnit_Framework_Assert::assertTrue(
            $giftCardAccountIndex->getGiftCardAccount()->isRowVisible($filter, false),
            'Gift card is absent in customer groups grid.'
        );
    }

    /**
     * Success assert of  gift card account in grid
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer gift card account in grid.';
    }
}
