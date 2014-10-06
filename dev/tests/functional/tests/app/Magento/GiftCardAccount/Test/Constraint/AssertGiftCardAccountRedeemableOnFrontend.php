<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Class AssertGiftCardAccountRedeemableOnFrontend
 * Assert that gift card is redeemable on frontend
 */
class AssertGiftCardAccountRedeemableOnFrontend extends AbstractAssertGiftCardAccountOnFrontend
{
    /**
     * Text value to be checked
     */
    const SUCCESS_MESSAGE = 'Gift Card "%s" was redeemed.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that gift card is redeemable on frontend
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CmsIndex $cmsIndex
     * @param CustomerInjectable $customer
     * @param GiftCardAccount $giftCardAccount
     * @param string $code
     * @return void
     */
    public function processAssert(
        CustomerAccountIndex $customerAccountIndex,
        CmsIndex $cmsIndex,
        CustomerInjectable $customer,
        GiftCardAccount $giftCardAccount,
        $code
    ) {
        $this->login($customer);
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Card');
        $customerAccountIndex->getRedeemBlock()->redeemGiftCard($code);
        $message = $customerAccountIndex->getMessages()->getSuccessMessages();
        $expectMessage = sprintf(self::SUCCESS_MESSAGE, $code);
        \PHPUnit_Framework_Assert::assertEquals(
            $message,
            $expectMessage,
            'Wrong success message is displayed.'
        );
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Store Credit');
        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountIndex->getStoreCreditBlock()->isBalanceChangeVisible($giftCardAccount->getBalance()),
            'Store credit is not change.'
        );
    }

    /**
     * Text that gift card is redeemable on frontend
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card is redeemable on frontend and success redeemed message is displayed.';
    }
}
