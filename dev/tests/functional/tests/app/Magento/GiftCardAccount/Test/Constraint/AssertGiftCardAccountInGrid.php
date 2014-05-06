<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
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
     * @param Index $index
     * @return void
     */
    public function processAssert(
        GiftCardAccount $giftCardAccount,
        Index $index
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        \PHPUnit_Framework_Assert::assertTrue(
            $index->getGiftCardAccount()->isRowVisible($filter, false),
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
