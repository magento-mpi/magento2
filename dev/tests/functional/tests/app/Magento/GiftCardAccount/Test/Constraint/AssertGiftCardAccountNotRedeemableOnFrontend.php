<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\GiftCardAccount\Test\Page\Adminhtml\Index;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Class AssertGiftCardAccountNotRedeemableOnFrontend
 * Assert that gift card is not redeemable on frontend
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
        if ($giftCardAccount->hasData('code')) {
            $value = $giftCardAccount->getCode();
        } else {
            $index->open();
            $filter = ['balance' => $giftCardAccount->getBalance()];
            $value = $index->getGiftCardAccount()->getCode($filter, false);
        }
        $this->login($cmsIndex, $customerAccountLogin, $customer);

        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Card');
        $customerAccountIndex->getRedeemBlock()->redeemGiftCard($value);

        \PHPUnit_Framework_Assert::assertTrue(
            $customerAccountIndex->getMessages()->assertErrorMessage(),
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
