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
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Class AssertGiftCardAccountNotRedeemableOnFrontend
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardAccountNotRedeemableOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card is not redeemable on frontend
     *
     * @param Index $index
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CmsIndex $cmsIndex
     * @param GiftCardAccount $giftCardAccount
     * @internal param CustomerAccountCreate $customerAccountCreate
     * @internal param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        Index $index,
        CustomerAccountIndex $customerAccountIndex,
        CmsIndex $cmsIndex,
        GiftCardAccount $giftCardAccount
    ) {
        $index->open();
        /** @var array $filter */
        $filter = ['balance' => $giftCardAccount->getBalance()];
        /** @var string $value */
        $value = $index->getGiftCardAccount()->searchCode($filter, false);

        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->selectGiftCard();
        $customerAccountIndex->getRedeemBlock()->fillGiftCardRedeem($value);
        $isActualMessage = $customerAccountIndex->getMessages()->assertErrorMessage();

        \PHPUnit_Framework_Assert::assertTrue(
            $isActualMessage,
            'Gift card is redeemable on frontend'
        );
    }

    /**
     * Text that gift card is not redeemable on frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card is not redeemable on frontend';
    }
}
