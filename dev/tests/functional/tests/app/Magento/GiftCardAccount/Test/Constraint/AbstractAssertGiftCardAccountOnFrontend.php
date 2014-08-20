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
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class AbstractAssertGiftCardAccountOnFrontend
 * Assert that created gift card account can be verified on the frontend
 */
abstract class AbstractAssertGiftCardAccountOnFrontend extends AbstractConstraint
{
    /**
     * Login on the frontend
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
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @param CustomerAccountIndex|CheckoutCart $page
     * @return array
     */
    protected function prepareData(array $data, $page)
    {
        $fixtureData = [
            'code' => $data['code'],
            'balance' => $data['balance'],
            'date_expires' => $data['date_expires']
        ];
        $pageData = $page->getCheckBlock()->getGiftCardAccountData($fixtureData);

        return ['fixtureData' => $fixtureData, 'pageData' => $pageData];
    }
}
