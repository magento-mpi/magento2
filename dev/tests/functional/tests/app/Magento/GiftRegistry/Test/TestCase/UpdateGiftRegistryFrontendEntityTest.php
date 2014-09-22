<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;

/**
 * Test Creation for Update frontend GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Gift Registry is created
 *
 * Steps:
 * 1. Go to frontend
 * 2. Login as a Customer
 * 3. Go to My Account -> Gift Registry
 * 4. Press on appropriate Gift Registry "Edit" button
 * 5. Edit data according to DataSet
 * 6. Edit data according to DataSet
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-26962
 */
class UpdateGiftRegistryFrontendEntityTest extends Injectable
{
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Gift Registry index page
     *
     * @var GiftRegistryIndex
     */
    protected $giftRegistryIndex;

    /**
     * Gift Registry edit type page
     *
     * @var GiftRegistryEdit
     */
    protected $giftRegistryEdit;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryEdit $giftRegistryEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryEdit $giftRegistryEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryEdit = $giftRegistryEdit;
    }

    /**
     * Create customer and product
     *
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(
        CatalogProductSimple $product,
        CustomerInjectable $customer
    ) {
        $product->persist();
        $customer->persist();

        return [
            'customer' => $customer,
            'product' => $product
        ];
    }

    /**
     * Update Gift Registry entity test
     *
     * @param GiftRegistry $giftRegistryOrigin
     * @param GiftRegistry $giftRegistry
     * @param CustomerInjectable $customer
     * @return void
     */
    public function test(GiftRegistry $giftRegistryOrigin, GiftRegistry $giftRegistry, CustomerInjectable $customer)
    {
        // Preconditions
        $this->customerAccountLogin->open()->getLoginBlock()->login($customer);
        $giftRegistryOrigin->persist();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Registry');
        $this->giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistryOrigin->getTitle(), 'Edit');
        $this->giftRegistryEdit->getCustomerEditForm()->fill($giftRegistry);
        $this->giftRegistryEdit->getActionsToolbarBlock()->save();
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
