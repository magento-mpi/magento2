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
use Magento\Customer\Test\Page\CustomerAccountLogout;

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
     * Customer log out page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Constructor
     *
     * @constructor
     * @param ObjectManager $objectManager
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogout $customerAccountLogout
     */
    public function __construct(
        ObjectManager $objectManager,
        CustomerAccountLogin $customerAccountLogin,
        CmsIndex $cmsIndex,
        CustomerAccountLogout $customerAccountLogout
    ) {
        parent::__construct($objectManager);
        $this->customerAccountLogin = $customerAccountLogin;
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Login on the frontend
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function login(CustomerInjectable $customer)
    {
        $this->customerAccountLogout->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
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
