<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;

/**
 * Test Creation for CreateOrderFromCustomerPage (comparedProducts)
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer
 * 2. Create products
 * 3. Add products to compare list
 *
 * Steps:
 * 1. Open Customers -> All Customers
 * 2. Search and open customer from preconditions
 * 3. Click 'Create Order'
 * 4. Check product in comparison list section
 * 5. Click 'Update Changes'
 * 6. Perform all assertions
 *
 * @group Order_Management_(CS)
 * @ZephyrId MAGETWO-28050
 */
class MoveProductsInComparedOnOrderPageTest extends Injectable
{
    /**
     * Array products
     *
     * @var array
     */
    protected $products;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Browser
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
     * Customer login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Fixture customer
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Order create index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

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
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerInjectable $customer
     * @return void
     */
    public function __prepare(FixtureFactory $fixtureFactory, CustomerInjectable $customer)
    {
        $this->fixtureFactory = $fixtureFactory;
        $customer->persist();
        $this->customer = $customer;
    }

    /**
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param CustomerAccountLogin $customerAccountLogin
     * @param OrderCreateIndex $orderCreateIndex
     * @param CustomerIndex $customerIndex
     * @param CustomerIndexEdit $customerIndexEdit
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        Browser $browser,
        CustomerAccountLogin $customerAccountLogin,
        OrderCreateIndex $orderCreateIndex,
        CustomerIndex $customerIndex,
        CustomerIndexEdit $customerIndexEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->browser = $browser;
        $this->orderCreateIndex = $orderCreateIndex;
        $this->customerIndex = $customerIndex;
        $this->customerIndexEdit = $customerIndexEdit;
    }

    /**
     * Move compare products on order page
     *
     * @param $products
     * @return array
     */
    public function test($products)
    {
        // Preconditions
        $products = $this->createProducts($products);
        $this->loginCustomer();
        $this->addProducts($products);

        // Steps:
        $this->customerIndex->open();
        $this->customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $this->customer->getEmail()]);
        $this->customerIndexEdit->getPageActionsBlock()->createOrder();
        $this->orderCreateIndex->getStoreBlock()->selectStoreView();
        $activitiesBlock =  $this->orderCreateIndex->getCustomerActivitiesBlock();
        $activitiesBlock->getProductsInComparisonBlock()->addToOrderByName($this->extractProductNames($products));
        $activitiesBlock->updateChanges();

        return ['entityData' => ['products' => $products], 'productsIsConfigured' => false];
    }

    /**
     * Login customer
     *
     * @return void
     */
    protected function loginCustomer()
    {
        $this->cmsIndex->open();
        if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($this->customer);
        }
    }

    /**
     * Create products
     *
     * @param string $products
     * @return array
     */
    protected function createProducts($products)
    {
        $products = explode(',', $products);
        foreach ($products as $key => $product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
            $products[$key] = $product;
        }
        return $products;
    }

    /**
     * Add products to compare list
     *
     * @param array $products
     * @return void
     */
    protected function addProducts(array $products)
    {
        foreach ($products as $itemProduct) {
            $this->browser->open($_ENV['app_frontend_url'] . $itemProduct->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->clickAddToCompare();
        }
    }

    /**
     * Extract products name
     *
     * @param array $data
     * @return array
     */
    protected function extractProductNames($data)
    {
        $result = [];
        foreach ($data as $product) {
            $result[] = $product->getName();
        }
        return $result;
    }
}
