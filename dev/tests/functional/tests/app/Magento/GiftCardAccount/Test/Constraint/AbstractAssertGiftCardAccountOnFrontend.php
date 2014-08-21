<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\ObjectManager;
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
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Constructor
     *
     * @constructor
     * @param ObjectManager $objectManager
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CmsIndex $cmsIndex
     */
    public function __construct(
        ObjectManager $objectManager,
        CustomerAccountLogin $customerAccountLogin,
        CmsIndex $cmsIndex
    ) {
        $this->objectManager = $objectManager;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Login on the frontend
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function login(CustomerInjectable $customer)
    {
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
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
