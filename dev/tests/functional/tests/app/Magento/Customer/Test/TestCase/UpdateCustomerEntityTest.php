<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\TestCase;

use Magento\Backend\Test\Page\Dashboard;
use Magento\Customer\Test\Constraint\AssertCustomerInfoSuccessSavedMessage;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountEdit;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAddressEdit;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;

/**
 * Test Creation for Update CustomerEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Default test customer is created
 *
 * Steps:
 * 1. Login to fronted as test customer from preconditions
 * 2. Navigate to Account Dashboard page:
 * 3. Click "Edit" link near "Contact Information"
 * 4. Fill fields with test data and save
 * 5. Click "Edit Address" link near "Default Billing Address", save and return to Account Dashboard page
 * 6. Fill fields with test data and save
 * 7. Perform all assertions
 *
 * @group Customer_Account_(CS)
 * @ZephyrId MAGETWO-25925
 */
class UpdateCustomerEntityTest extends Injectable
{
    /**
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * @var CustomerAccountEdit
     */
    protected $customerAccountEdit;

    /**
     * @var CustomerAddressEdit
     */
    protected $customerAddressEdit;

    /**
     * Preparing data for test
     *
     * @param CmsIndex $cmsIndex
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountEdit $customerAccountEdit
     * @param CustomerAddressEdit $customerAddressEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        FixtureFactory $fixtureFactory,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountEdit $customerAccountEdit,
        CustomerAddressEdit $customerAddressEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->fixtureFactory = $fixtureFactory;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->customerAccountEdit = $customerAccountEdit;
        $this->customerAddressEdit = $customerAddressEdit;

    }

    /**
     * Runs Update Customer Entity test
     *
     * @param CustomerInjectable $customerInit
     * @param AddressInjectable $address
     * @param AssertCustomerInfoSuccessSavedMessage $assertCustomerInfoSuccessSavedMessage
     * @return void
     */
    public function test(
        CustomerInjectable $customerInit,
        AddressInjectable $address,
        AssertCustomerInfoSuccessSavedMessage $assertCustomerInfoSuccessSavedMessage
    ) {
        // Preconditions
        $customer = $this->fixtureFactory->createByCode('customerInjectable', ['dataSet' => 'default']);
        $customer->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink('Log In');
        $this->customerAccountLogin->getLoginBlock()->fill($customer);
        $this->customerAccountLogin->getLoginBlock()->submit();

        $this->customerAccountIndex->getInfoBlock()->openEditContactInfo();
        $this->customerAccountEdit->getAccountInfoForm()->fill($customerInit);
        $this->customerAccountEdit->getAccountInfoForm()->submit();

        $assertCustomerInfoSuccessSavedMessage->configure($this, ['customerAccountIndex' => $this->customerAccountIndex]);
        \PHPUnit_Framework_Assert::assertThat($this->getName(), $assertCustomerInfoSuccessSavedMessage);

        $this->customerAccountIndex->getDashboardAddress()->editBillingAddress();
        $this->customerAddressEdit->getEditForm()->fill($address);
        $this->customerAddressEdit->getEditForm()->saveAddress();
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
