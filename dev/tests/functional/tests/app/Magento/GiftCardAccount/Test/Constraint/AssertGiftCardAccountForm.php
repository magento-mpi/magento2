<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\GiftCardAccount\Test\Page\Adminhtml\NewIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;

/**
 * Class AssertGiftCardAccountForm
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardAccountForm extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card account equals to passed from fixture
     *
     * @param GiftCardAccount $giftCardAccount
     * @param Index $index
     * @param NewIndex $newIndex
     * @return void
     */
    public function processAssert(
        GiftCardAccount $giftCardAccount,
        Index $index,
        NewIndex $newIndex
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        $index->getGiftCardAccount()->searchAndSelect($filter, false);

        \PHPUnit_Framework_Assert::assertTrue(
            $newIndex->getPageMainForm()->verify($giftCardAccount),
            'Gift card account not equals to passed from fixture.'
        );
    }

    /**
     * Success assert of  gift card account equals to passed from fixture
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account equals to passed from fixture.';
    }
}
