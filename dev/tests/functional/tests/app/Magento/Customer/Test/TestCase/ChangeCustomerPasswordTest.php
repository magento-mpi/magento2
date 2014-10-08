<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountEdit;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;

/**
 * Test Creation for ChangeCustomerPassword
 *
 * Test Flow:
 * Preconditions:
 * 1. Default test customer is created
 *
 * Steps:
 * 1. Login to fronted as test customer from preconditions
 * 2. Navigate to Account Dashboard page:
 * 3. Click "Change Password" link near "Contact Information"
 * 4. Fill fields with test data and save
 * 5. Perform all assertions
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MAGETWO-29411
 */
class ChangeCustomerPasswordTest extends Injectable
{
    /**
     * CmsIndex page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * CustomerAccountLogin page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * CustomerAccountIndex page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * CustomerAccountEdit page
     *
     * @var CustomerAccountEdit
     */
    protected $customerAccountEdit;


    /**
     * Preparing pages for test
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountEdit $customerAccountEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountEdit $customerAccountEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerAccountEdit = $customerAccountEdit;
    }

    /**
     * Run Change Customer Password test
     *
     * @param CustomerInjectable $initialCustomer
     * @param CustomerInjectable $customer
     * @return void
     */
    public function test(
        CustomerInjectable $initialCustomer,
        CustomerInjectable $customer
    ) {
        // Preconditions
        $initialCustomer->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Log In');
        $this->customerAccountLogin->getLoginBlock()->login($initialCustomer);

        $this->customerAccountIndex->getInfoBlock()->openChangePassword();
        $this->customerAccountEdit->getAccountInfoForm()->fill($customer);
        $this->customerAccountEdit->getAccountInfoForm()->submit();
    }

    /**
     * Customer logout from account
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->cmsIndex->getLinksBlock()->isVisible()) {
            $this->cmsIndex->getLinksBlock()->openLink('Log Out');
        }
    }
}
