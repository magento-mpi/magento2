<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryAddSelect;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\GiftRegistry\Test\Page\GiftRegistryShare;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\GiftRegistry\Test\Page\CheckoutCart;

/**
 * Test Creation for Sharing GiftRegistryEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Simple product is created
 * 2. Two Customer created on the front
 * 3. Gift Registry Search widget was configured
 *
 * Steps:
 * 1. Login as registered customer1 to frontend
 * 2. Create Gift Registry
 * 3. Add product to shopping cart
 * 4. Add product from cart to GiftRegistry
 * 5. Share Gift Registry with customer2
 * 6. Login as customer2
 * 7. Fill search data to Gift Registry Search widget according to dataSet
 * 8. Click button Search
 * 9. Perform all assertions
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-27035
 */
class SharingGiftRegistryEntityTest extends Injectable
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
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Page CustomerAccountLogout
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

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
     * Product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CheckoutCart
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Gift Registry share page
     *
     * @var GiftRegistryShare
     */
    protected $giftRegistryShare;

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryAddSelect $giftRegistryAddSelect
     * @param GiftRegistryEdit $giftRegistryEdit
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param GiftRegistryShare $giftRegistryShare
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryAddSelect $giftRegistryAddSelect,
        GiftRegistryEdit $giftRegistryEdit,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        GiftRegistryShare $giftRegistryShare
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryAddSelect = $giftRegistryAddSelect;
        $this->giftRegistryEdit = $giftRegistryEdit;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->giftRegistryShare = $giftRegistryShare;
    }

    /**
     * Create product, customers and search gift registry widget
     *
     * @param CatalogProductSimple $product
     * @param CustomerInjectable $customer1
     * @param CustomerInjectable $customer2
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(
        CatalogProductSimple $product,
        CustomerInjectable $customer1,
        CustomerInjectable $customer2,
        FixtureFactory $fixtureFactory
    ) {
        $product->persist();
        $customer1->persist();
        $customer2->persist();
        $fixtureFactory->createByCode('widget', ['dataSet' => 'gift_registry_search'])->persist();

        return [
            'product' => $product,
            'customer1' => $customer1,
            'customer2' => $customer2
        ];
    }

    /**
     * Sharing Gift Registry entity test
     *
     * @param CustomerInjectable $customer1
     * @param CustomerInjectable $customer2
     * @param GiftRegistry $giftRegistry
     * @param CatalogProductSimple $product
     * @param string $message
     * @param string $searchType
     * @return void
     */
    public function test(
        CustomerInjectable $customer1,
        CustomerInjectable $customer2,
        GiftRegistry $giftRegistry,
        CatalogProductSimple $product,
        $message,
        $searchType
    ) {
        // Steps
        // Login as registered customer1 to frontend
        $this->cmsIndex->open();
        if ($this->cmsIndex->getLinksBlock()->isLinkVisible("Log Out")) {
            $this->customerAccountLogout->open();
        }
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer1);
        // Create Gift Registry
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Gift Registry");
        $this->giftRegistryIndex->getActionsToolbar()->addNew();
        $this->giftRegistryAddSelect->getGiftRegistryTypeBlock()->selectGiftRegistryType($giftRegistry->getTypeId());
        $this->giftRegistryEdit->getCustomerEditForm()->fill($giftRegistry);
        $this->giftRegistryEdit->getActionsToolbarBlock()->save();
        // Add product to shopping cart
        $this->catalogProductView->init($product);
        $this->catalogProductView->open()->getViewBlock()->clickAddToCart();
        // Add product from cart to GiftRegistry
        $this->checkoutCart->getGiftRegistryCart()->addToGiftRegistry($giftRegistry->getTitle());
        // Share Gift Registry with customer2
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Gift Registry");
        $this->giftRegistryIndex->getGiftRegistryGrid()->eventAction($giftRegistry->getTitle(), 'Share');
        $this->giftRegistryShare->getGiftRegistryShareForm()->setSenderMessage($message);
        $this->giftRegistryShare->getGiftRegistryShareForm()->fill($customer2);
        $this->giftRegistryShare->getGiftRegistryShareForm()->shareGiftRegistry();
        // Login as customer2
        $this->customerAccountLogout->open();
        $this->cmsIndex->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer2);
        // Fill search data to Gift Registry Search widget according to dataSet
        $this->customerAccountIndex->getGiftRegistrySearchWidgetForm()->selectSearchType($searchType);
        $this->customerAccountIndex->getGiftRegistrySearchWidgetForm()
            ->fillForm($customer2, $giftRegistry->getTypeId());
        // Click button Search
        $this->customerAccountIndex->getGiftRegistrySearchWidgetForm()->clickSearch();
    }
}
