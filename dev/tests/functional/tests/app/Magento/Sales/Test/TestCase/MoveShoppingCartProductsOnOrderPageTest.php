<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\TestCase;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Create order from customer page(cartActions)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create product
 * 3. Add product to cart
 *
 * Steps:
 * 1. Open Customers -> All Customers
 * 2. Search and open customer from preconditions
 * 3. Click Create Order
 * 4. Check product in Shopping Cart section
 * 5. Click Update Changes
 * 6. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28540
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MoveShoppingCartProductsOnOrderPageTest extends Injectable
{
    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer logout page
     *
     * @var CustomerAccountLogout
     */
    protected $customerAccountLogout;

    /**
     * Browser instance
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Customer index page
     *
     * @var CustomerIndex
     */
    protected $customerIndex;

    /**
     * Customer index edit page
     *
     * @var CustomerIndexEdit
     */
    protected $customerIndexEdit;

    /**
     * Order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Prepare data
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
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @param CatalogProductView $catalogProductView
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @param OrderCreateIndex $orderCreateIndex
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CatalogProductView $catalogProductView,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit,
        FixtureFactory $fixtureFactory,
        Browser $browser,
        OrderCreateIndex $orderCreateIndex
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->customerAccountLogout = $customerAccountLogout;
        $this->catalogProductView = $catalogProductView;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
        $this->fixtureFactory = $fixtureFactory;
        $this->browser = $browser;
        $this->orderCreateIndex = $orderCreateIndex;
    }

    /**
     * Create order from customer page(cartActions)
     *
     * @param CustomerInjectable $customer
     * @param string $product
     * @return array
     */
    public function test(CustomerInjectable $customer, $product)
    {
        //Preconditions
        list($fixture, $dataSet) = explode('::', $product);
        $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
        $product->persist();
        $this->cmsIndex->open()->getLinksBlock()->openLink("Log In");
        $this->customerAccountLogin->getLoginBlock()->login($customer);
        $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->addToCart($product);

        //Steps
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
        $this->orderCreateIndex->getStoreBlock()->selectStoreView();
        $this->orderCreateIndex->getCustomerActivitiesBlock()->getShoppingCartItemsBlock()
            ->addToOrderByName($product->getName());
        $this->orderCreateIndex->getCustomerActivitiesBlock()->updateChanges();

        return ['products' => [$product]];
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
