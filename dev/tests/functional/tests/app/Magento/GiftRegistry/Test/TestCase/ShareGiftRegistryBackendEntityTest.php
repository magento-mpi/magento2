<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;

/**
 * Test Creation for Share Backend GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Customer is created on the frontend
 * 2. Gift registry is created
 *
 * Steps:
 * 1. Login to backend
 * 2. Open Customers->All Customers
 * 3. Search and open created in preconditions customer
 * 4. Open Gift Registry tab
 * 5. Open created in preconditions gift registry
 * 6. Fill "Sharing info" sections according to dataSet
 * 7. Click Share Gift Registry
 * 8. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27225
 */
class ShareGiftRegistryBackendEntityTest extends Injectable
{
    /**
     * Customer index page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Customer edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Gift registry edit page
     *
     * @var GiftRegistryCustomerEdit
     */
    protected $giftRegistryCustomerEdit;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer account login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Create customer
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
        $customer->persist();

        return [
            'customer' => $customer,
        ];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        GiftRegistryCustomerEdit $giftRegistryCustomerEdit,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->giftRegistryCustomerEdit = $giftRegistryCustomerEdit;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
    }

    /**
     * Share Gift Registry from Customer Account(Backend)
     *
     * @param GiftRegistry $giftRegistry
     * @param CustomerInjectable $customer
     * @param array $sharingInfo
     * @return void
     */
    public function test(GiftRegistry $giftRegistry, CustomerInjectable $customer, $sharingInfo)
    {
        // Preconditions
        $this->customerAccountLogin->open()->getLoginBlock()->login($customer);
        $giftRegistry->persist();

        // Steps
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $customerForm = $this->customerIndexEdit->getCustomerForm();
        $customerForm->openTab('gift_registry');
        $filter = ['title' => $giftRegistry->getTitle()];
        $customerForm->getTabElement('gift_registry')->getSearchGridBlock()->searchAndOpen($filter);
        $this->giftRegistryCustomerEdit->getSharingInfoBlock()->fillForm($sharingInfo);
        $this->giftRegistryCustomerEdit->getSharingInfoBlock()->shareGiftRegistry();
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }
}
