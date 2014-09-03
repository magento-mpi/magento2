<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Cms\Test\Page\CmsIndex;

/**
 * Class AssertGiftCardAccountNotRedeemableOnFrontend
 * Assert that gift card is not redeemable on frontend
 */
class AssertGiftCardAccountNotRedeemableOnFrontend extends AbstractAssertGiftCardAccountOnFrontend
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
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CmsIndex $cmsIndex
     * @param CustomerInjectable $customer
     * @param string $code
     * @return void
     */
    public function processAssert(
        CustomerAccountIndex $customerAccountIndex,
        CmsIndex $cmsIndex,
        CustomerInjectable $customer,
        $code
    ) {
        $this->login($customer);
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Card');
        $customerAccountIndex->getRedeemBlock()->redeemGiftCard($code);

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
}
