<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;

/**
 * Class AssertGiftCardAccountStatusInCustomerAccount
 * Assert that created gift card account can be verified on the frontend on My Account page
 */
class AssertGiftCardAccountStatusInCustomerAccount extends AbstractAssertGiftCardAccountOnFrontend
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that created gift card account can be verified on the frontend on My Account page
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftCardAccount $giftCardAccount
     * @param CustomerInjectable $customer
     * @param string $code
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        GiftCardAccount $giftCardAccount,
        CustomerInjectable $customer,
        $code
    ) {
        $this->login($customer);
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Card');
        $data = $giftCardAccount->getData();
        $data['code'] = $code;
        $customerAccountIndex->getRedeemBlock()->checkStatusAndBalance($data['code']);
        $result = $this->prepareData($data, $customerAccountIndex);
        \PHPUnit_Framework_Assert::assertEquals(
            $result['fixtureData'],
            $result['pageData'],
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account data is correct on the frontend on My Account page.';
    }
}
