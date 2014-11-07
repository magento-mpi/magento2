<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestStep;

use Mtf\TestStep\TestStepInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class LoginCustomerOnFrontendStep
 * Login customer on frontend
 */
class LoginCustomerOnFrontendStep implements TestStepInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerInjectable $customer
     */
    public function __construct(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerInjectable $customer
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customer = $customer;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Login customer
     *
     * @return void
     */
    public function run()
    {
        $this->customerAccountLogout->open();
        $this->cmsIndex->getCmsPageBlock()->waitUntilTextIsVisible('Home Page');
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($this->customer);
    }
}
