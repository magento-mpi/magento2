<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\TestCase;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\GiftRegistry\Test\Fixture\GiftRegistry;
use Magento\GiftRegistry\Test\Page\Adminhtml\GiftRegistryCustomerEdit;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\InjectableFixture;

/**
 * Test Creation for Updating Items of GiftRegistryEntity from Customer Account(Backend)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Gift Registry is created
 * 3. Products are created
 * 4. Created product is added to GiftRegistry
 *
 * Steps:
 * 1. Go to backend
 * 2. Go to Customers -> All Customers
 * 3. Open GiftRegistry tab
 * 4. Press on appropriate Gift Registry "Edit" button
 * 5. Edit data according to DataSet
 * 6. Click "Update Items and Qty's" button
 * 7. Perform Asserts
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-28331
 */
class UpdateGiftRegistryItemsBackendEntityTest extends Injectable
{
    /**
     * Customer Index page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Customer Edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Customer Account Login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer Account Logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * GiftRegistry Customer Edit page
     *
     * @var GiftRegistryCustomerEdit
     */
    protected $giftRegistryCustomerEdit;

    /**
     * Catalog Product View page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Checkout Cart page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * CmsIndex page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * FixtureFactory object
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Prepare data for test
     *
     * @param CustomerInjectable $customer
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(CustomerInjectable $customer, FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
        $customer->persist();
        return ['customer' => $customer];
    }

    /**
     * Prepare pages for test
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param GiftRegistryCustomerEdit $giftRegistryCustomerEdit
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        GiftRegistryCustomerEdit $giftRegistryCustomerEdit,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->giftRegistryCustomerEdit = $giftRegistryCustomerEdit;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Update Gift Registry entity test
     *
     * @param CustomerInjectable $customer
     * @param GiftRegistry $giftRegistry
     * @param Browser $browser
     * @param string $products
     * @param string $qty
     * @param string $actions
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        GiftRegistry $giftRegistry,
        Browser $browser,
        $products,
        $qty,
        $actions
    ) {
        // Preconditions:
        $qty = explode(",", $qty);
        $actions = explode(',', $actions);
        // Creating products
        $products = $this->createProducts($products);
        // Login as customer
        $this->loginAsCustomer($customer);
        // Creating gift registry
        $giftRegistry->persist();
        // Adding products to gift registry
        foreach ($products as $product) {
            $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->addToCart($product);
            $this->checkoutCart->getGiftRegistryCart()->addToGiftRegistry($giftRegistry->getTitle());
        }

        // Steps:
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $customerForm = $this->customerIndexEdit->getCustomerForm();
        $customerForm->openTab('gift_registry');
        $filter = ['title' => $giftRegistry->getTitle()];
        $customerForm->getTabElement('gift_registry')->getSearchGridBlock()->searchAndOpen($filter);
        $this->updateGiftRegistryItems($products, $qty, $actions);

        $products = $this->prepareProducts($products, $actions);
        return ['products' => $products];
    }

    /**
     * Tear down after variation
     *
     * @return void
     */
    public function tearDown()
    {
        $this->customerAccountLogout->open();
    }

    /**
     * Create products
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        $products = explode(",", $products);
        $createdProducts = [];
        foreach ($products as $product) {
            list($fixture, $dataSet) = explode("::", $product);
            $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
            $createdProducts[] = $product;
        }
        return $createdProducts;
    }

    /**
     * Prepare products for constraints
     *
     * @param array $products
     * @param array $actions
     * @return array
     */
    protected function prepareProducts(array $products, array $actions)
    {
        $deletedProducts = array_keys($actions, 'Remove Item');
        foreach ($deletedProducts as $index) {
            unset($products[$index]);
        }
        return $products;
    }

    /**
     * Login as customer
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function loginAsCustomer(CustomerInjectable $customer)
    {
        $this->cmsIndex->open()->getLinksBlock()->openLink('Log In');
        $this->customerAccountLogin->getLoginBlock()->login($customer);
    }

    /**
     * Update gift registry items
     *
     * @param InjectableFixture[] $products
     * @param array $qty
     * @param array $actions
     * @return void
     */
    protected function updateGiftRegistryItems(array $products, array $qty, array $actions)
    {
        $productsProperties = [];
        foreach ($products as $key => $product) {
            $productsProperties[] = [
                'name' => $product->getName(),
                'qty' => $qty[$key],
                'action' => $actions[$key]
            ];
        }
        $this->giftRegistryCustomerEdit->getItemsGrid()->searchAndUpdate($productsProperties);
    }
}
