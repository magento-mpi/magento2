<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\AddressInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\GiftRegistryAddSelect;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 *
 * Steps:
 * 1. Go to frontend
 * 2. Login as a Customer
 * 3. Go to My Account -> Gift Registry
 * 4. Press button "Add New"
 * 5. Choose Gift Registry type from DataSet
 * 6. Press next
 * 7. Fill data from DataSet
 * 8. Perform Asserts
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-26176
 */
class CreateGiftRegistryEntityTest extends Injectable
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
     * Customer from precondition
     *
     * @var CustomerInjectable
     */
    protected $customer;

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
     * Gift Registry select type page
     *
     * @var GiftRegistryAddSelect
     */
    protected $giftRegistryAddSelect;

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
     * @param CustomerAccountLogin $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryAddSelect $giftRegistryAddSelect
     * @param GiftRegistryEdit $giftRegistryEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryAddSelect $giftRegistryAddSelect,
        GiftRegistryEdit $giftRegistryEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryAddSelect = $giftRegistryAddSelect;
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
     * Create Gift Registry entity test
     *
     * @param GiftRegistry $giftRegistry
     * @param AddressInjectable $address
     * @param CustomerInjectable $customer
     * @return void
     */
    public function test(
        GiftRegistry $giftRegistry,
        AddressInjectable $address,
        CustomerInjectable $customer
    ) {
        // Steps
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible("Log Out")) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Gift Registry");
        $this->giftRegistryIndex->getActionsToolbar()->addNew();
        $this->giftRegistryAddSelect->getGiftRegistryTypeBlock()->selectGiftRegistryType($giftRegistry->getTypeId());
        $this->giftRegistryEdit->getCustomerEditForm()->fill($giftRegistry);
        $this->giftRegistryEdit->getShippingAddressForm()->fill($address);
        $this->giftRegistryEdit->getActionsToolbarBlock()->save();
    }
}
