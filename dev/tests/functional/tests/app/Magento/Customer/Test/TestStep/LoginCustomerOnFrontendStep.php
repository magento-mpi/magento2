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
use \Mtf\ObjectManager;

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
     * @var \Mtf\ObjectManager
     */
    protected $objectManager;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerInjectable $customer
     * @param ObjectManager $objectManager
     */
    public function __construct(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerInjectable $customer,
        ObjectManager $objectManager
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customer = $customer;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->objectManager = $objectManager;
    }

    /**
     * Login customer
     *
     * @return void
     */
    public function run()
    {
        $this->customerAccountLogout->open();
        $this->cmsIndex->open();
        if ($this->cmsIndex->getLinksBlock()->isLinkVisible("Log Out")) {
            $this->cmsIndex->getLinksBlock()->openLink("Log Out");
        }
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($this->customer);
    }
}
