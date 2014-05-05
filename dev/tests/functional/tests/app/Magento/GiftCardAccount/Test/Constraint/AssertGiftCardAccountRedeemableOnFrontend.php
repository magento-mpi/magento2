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
use Magento\GiftCardAccount\Test\Page\Adminhtml\GiftCardAccountIndex;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Class AssertGiftCardAccountRedeemableOnFrontend
 *
 * @package Magento\GiftCardAccount\Test\Constraint
 */
class AssertGiftCardAccountRedeemableOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card is redeemable on frontend
     *
     * @param GiftCardAccountIndex $giftCardAccountIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CmsIndex $cmsIndex
     * @param GiftCardAccount $giftCardAccount
     * @internal param CustomerAccountCreate $customerAccountCreate
     * @internal param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        GiftCardAccountIndex $giftCardAccountIndex,
        CustomerAccountIndex $customerAccountIndex,
        CmsIndex $cmsIndex,
        GiftCardAccount $giftCardAccount
    ) {
        $giftCardAccountIndex->open();
        /** @var array $filter */
        $filter = ['balance' => $giftCardAccount->getBalance()];
        /** @var string $value */
        $value = $giftCardAccountIndex->getGiftCardAccount()->searchCode($filter, false);

        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->selectGiftCard();
        $customerAccountIndex->getRedeemBlock()->fillGiftCardRedeem($value);
        $isActualMessage = $customerAccountIndex->getMessages()->assertSuccessMessage();

        \PHPUnit_Framework_Assert::assertTrue(
            $isActualMessage,
            'Gift card is not redeemable on frontend'
        );
    }

    /**
     * Text that gift card is redeemable on frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card is redeemable on frontend';
    }
}
