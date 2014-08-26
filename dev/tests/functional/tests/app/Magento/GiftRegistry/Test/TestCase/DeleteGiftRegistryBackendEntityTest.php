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
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;

/**
 * Test Creation for DeleteGiftRegistryEntity from Customer Account(Backend)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create Gift Registry
 *
 * Steps:
 * 1. Login to backend
 * 2. Go to Customers->All Customers
 * 3. Search and open created customer
 * 4. Navigate to Gift Registry tab
 * 5. Search and open gift registry created in preconditions
 * 6. Click button "Delete Registry"
 * 7. Perform all asserts
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27034
 */
class DeleteGiftRegistryBackendEntityTest extends Injectable
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
     * Create product
     *
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CatalogProductSimple $product, CustomerInjectable $customer)
    {
        $product->persist();
        $customer->persist();

        return [
            'product' => $product,
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
     * Delete gift registry from customer account(backend)
     *
     * @param GiftRegistry $giftRegistry
     * @param CustomerInjectable $customer
     * @return void
     */
    public function test(GiftRegistry $giftRegistry, CustomerInjectable $customer)
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
        $this->giftRegistryCustomerEdit->getActionsToolbarBlock()->delete();
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
