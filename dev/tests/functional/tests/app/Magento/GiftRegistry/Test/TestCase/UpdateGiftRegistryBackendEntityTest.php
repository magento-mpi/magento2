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

/**
 * Test Creation for UpdateGiftRegistryEntity from Customer Account(Backend)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Register Customer
 * 2. Gift Registry is created
 * 3. Products are created
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
 */
class UpdateGiftRegistryBackendEntityTest extends Injectable
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
     * Prepare customer for test
     *
     * @param CustomerInjectable $customer
     * @return array
     */
    public function __prepare(CustomerInjectable $customer)
    {
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
     * @param FixtureFactory $fixtureFactory
     * @param string $product
     * @param string $qty
     * @return array
     */
    public function test(
        CustomerInjectable $customer,
        GiftRegistry $giftRegistry,
        Browser $browser,
        FixtureFactory $fixtureFactory,
        $product,
        $qty
    ) {
        // Preconditions:
        // Creating product
        list($fixture, $dataSet) = explode("::", $product);
        $product = $fixtureFactory->createByCode(
            $fixture,
            [
                'dataSet' => $dataSet,
                'data' => [
                    'checkout_data' => [
                        'preset' => 'default',
                        'value' => [
                            'qty' => $qty
                        ]
                    ]
                ]
            ]
        );
        $product->persist();
        // Creating gift registry
        $this->cmsIndex->open()->getLinksBlock()->openLink('Log In');
        $this->customerAccountLogin->getLoginBlock()->login($customer);
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
                'productName' => $product->getName()
            ]
        ];
        $cartItemsGrid->massaction($filter, 'Add to Gift Registry');

        return ['product' => $product];
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
}
