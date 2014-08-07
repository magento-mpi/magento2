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
class DeleteGiftRegistryEntityBackendTest extends Injectable
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
     * Create product
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function __prepare(CatalogProductSimple $product)
    {
        $product->persist();

        return ['product' => $product];
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        GiftRegistryCustomerEdit $giftRegistryCustomerEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->giftRegistryCustomerEdit = $giftRegistryCustomerEdit;
    }

    /**
     * Delete gift registry from customer account(backend)
     *
     * @param GiftRegistry $giftRegistry
     * @return void
     */
    public function test(GiftRegistry $giftRegistry)
    {
        $giftRegistry->persist();
        $this->customerIndex->open()->getCustomerGridBlock()->searchAndOpen(
            ['email' => $giftRegistry->getDataFieldConfig('customer_id')['source']->getCustomerId()->getEmail()]
        );
        $customerForm = $this->customerIndexEdit->getCustomerForm();
        $customerForm->openTab('gift_registry');
        $customerForm->getTabElement('gift_registry')->fillFormTab($giftRegistry->getData());
        $this->giftRegistryCustomerEdit->getActionsToolbarBlock()->delete();
    }

    /**
     * Log out after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log Out");
    }
}
