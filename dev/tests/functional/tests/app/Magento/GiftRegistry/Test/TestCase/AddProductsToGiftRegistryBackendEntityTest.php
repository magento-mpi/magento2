<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
use Mtf\ObjectManager;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for AddProductsToGiftRegistryEntity from Customer Account(Backend)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Gift Registry is created
 * 3. Product is created
 * 4. Created product is added to Shopping Cart
 *
 * Steps:
 * 1. Go to backend
 * 2. Go to Customers -> All Customers
 * 3. Open GiftRegistry tab
 * 4. Press on appropriate Gift Registry "Edit" button
 * 5. Edit data according to DataSet
 * 6. Perform Asserts
 *
 * @group Gift_Registry_(CS)
 * @ZephyrId MAGETWO-28215
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AddProductsToGiftRegistryBackendEntityTest extends Injectable
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
     * ObjectManager object
     *
     * @var ObjectManager
     */
    protected $objectManager;

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
     * @param ObjectManager $objectManager
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
        CheckoutCart $checkoutCart,
        ObjectManager $objectManager
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->giftRegistryCustomerEdit = $giftRegistryCustomerEdit;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->objectManager = $objectManager;
    }

    /**
     * Update Gift Registry entity test
     *
     * @param CustomerInjectable $customer
     * @param GiftRegistry $giftRegistry
     * @param Browser $browser
     * @param string $product
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        GiftRegistry $giftRegistry,
        Browser $browser,
        $product
    ) {
        // Preconditions:
        // Creating product
        $createProductsStep = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $product]
        );
        $product = $createProductsStep->run()['products'][0];
        // Creating gift registry
        $loginCustomerStep = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        );
        $loginCustomerStep->run();
        $giftRegistry->persist();
        // Adding product to cart
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->addToCart($product);

        // Steps:
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $customerForm = $this->customerIndexEdit->getCustomerForm();
        $customerForm->openTab('gift_registry');
        $filter = ['title' => $giftRegistry->getTitle()];
        $customerForm->getTabElement('gift_registry')->getSearchGridBlock()->searchAndOpen($filter);
        $cartItemsGrid = $this->giftRegistryCustomerEdit->getCartItemsGrid();
        $filter = [
            'products' => [
                'productName' => $product->getName(),
            ],
        ];
        $cartItemsGrid->massaction($filter, 'Add to Gift Registry', true);
        return ['products' => [$product]];
    }
}
