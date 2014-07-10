<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;
use Magento\Catalog\Test\Constraint\AssertProductCompareSuccessAddMessage;

/**
 * Test creation for adding CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. All product types are created
 * 2. Customer created
 *
 * Steps:
 * 1. Navigate to front-end
 * 1.1 If present data for customer, login as test customer
 * 2. Open product page of test product(s) and click "Add to Compare" button
 * 3. Assert success message is present on page
 * 4. Navigate to compare page(click "compare product" link at the top of the page)
 * 5. Perform all asserts
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-25843
 */
class AddCompareProductsTest extends Injectable
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
     * Catalog product compare page
     *
     * @var CatalogProductCompare
     */
    protected $catalogProductCompare;

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
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductCompare $catalogProductCompare
     * @param CatalogProductView $catalogProductView
     * @param CustomerAccountLogin $customerAccountLogin
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductCompare $catalogProductCompare,
        CatalogProductView $catalogProductView,
        CustomerAccountLogin $customerAccountLogin,
        FixtureFactory $fixtureFactory
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductCompare = $catalogProductCompare;
        $this->catalogProductView = $catalogProductView;
        $this->customerAccountLogin = $customerAccountLogin;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Test creation for adding compare products
     *
     * @param string $products
     * @param CustomerInjectable $customer
     * @param string $isCustomerLoggedIn
     * @param AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
     * @return array
     */
    public function test(
        $products,
        CustomerInjectable $customer,
        $isCustomerLoggedIn,
        AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
    ) {
        //Steps
        $this->cmsIndex->open();
        if ($isCustomerLoggedIn == 'Yes') {
            $customer->persist();
            if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
                $this->cmsIndex->getLinksBlock()->openLink("Log In");
                $this->customerAccountLogin->getLoginBlock()->login($customer);
            }
        }
        $this->products = $this->createProducts($products);
        $this->addProducts($this->products, $assertProductCompareSuccessAddMessage);
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");

        return ['products' => $this->products];
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
        foreach ($products as &$product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $this->fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
        }
        return $products;
    }

    /**
     * Add products to compare list
     *
     * @param array $products
     * @param AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
     * @return void
     */
    protected function addProducts(
        array $products,
        AssertProductCompareSuccessAddMessage $assertProductCompareSuccessAddMessage
    ) {
        foreach ($products as $itemProduct) {
            $this->catalogProductView->init($itemProduct);
            $this->catalogProductView->open();
            $this->catalogProductView->getViewBlock()->clickAddToCompare();
            $assertProductCompareSuccessAddMessage->configure(
                $this,
                ['catalogProductView' => $this->catalogProductView, 'product' => $itemProduct]
            );
            \PHPUnit_Framework_Assert::assertThat($this->getName(), $assertProductCompareSuccessAddMessage);
        }
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        // TODO after fix bug MAGETWO-22756 delete first step
        $this->cmsIndex->open();
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        for ($i = 1; $i <= count($this->products); $i++) {
            $this->catalogProductCompare->getCompareProductsBlock()->removeProduct();
        }
    }
}
