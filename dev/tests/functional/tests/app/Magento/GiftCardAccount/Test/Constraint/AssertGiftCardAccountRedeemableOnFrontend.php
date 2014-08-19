<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Class AssertGiftCardAccountRedeemableOnFrontend
 * Assert that gift card is redeemable on frontend
 */
class AssertGiftCardAccountRedeemableOnFrontend extends AbstractConstraint
{
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
     * @param Index $index
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerInjectable $customer
     * @param GiftCardAccount $giftCardAccount
     * @return void
     */
    public function processAssert(
        Index $index,
        CustomerAccountIndex $customerAccountIndex,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerInjectable $customer,
        GiftCardAccount $giftCardAccount
    ) {
        $index->open();
        $filter = ['balance' => $giftCardAccount->getBalance()];
        $value = $index->getGiftCardAccount()->getCode($filter, false);
        $this->login($cmsIndex, $customerAccountLogin, $customer);
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Card');
        $customerAccountIndex->getRedeemBlock()->redeemGiftCard($value);
        $message = $customerAccountIndex->getMessages()->getSuccessMessages();
        $expectMessage = sprintf(self::SUCCESS_MESSAGE, $value);
        \PHPUnit_Framework_Assert::assertEquals(
            $message,
            $expectMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . $expectMessage
            . "\nActual: " . $message
        );

        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Store Credit');
        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountIndex->getStoreCreditBlock()->isBalanceChangeVisible($filter['balance']),
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

    /**
     * Login to frontend
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function login(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerInjectable $customer
    ) {
        $cmsIndex->open();
        if (!$cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $cmsIndex->getLinksBlock()->openLink("Log In");
            $customerAccountLogin->getLoginBlock()->login($customer);
        }
        $cmsIndex->getLinksBlock()->openLink("My Account");
    }
}
