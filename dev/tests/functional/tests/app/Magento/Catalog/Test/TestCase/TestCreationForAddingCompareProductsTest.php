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
use Magento\Catalog\Test\Fixture\CatalogProductSimple\Price;
use Magento\Catalog\Test\Constraint\AssertProductCompareSuccessAddedMessage;

/**
 * Test creation for adding CompareProducts
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Products from products/dataSet is created
 *       simple_for_composite_products
 *       virtual_product
 *       gift_card_product
 *       downloadable_product
 *       grouped_product
 *       configurable_product
 *       bundle_dynamic_product
 *       bundle_fixed_product
 * 2. customer from customer/dataSet is created
 *       default
 *
 * Steps:
 * 1. Navigate to front-end
 * 1.1 If present data for customer, login as test customer
 * 2. Open product page of test product(s) and click "Add to Compare" button
 * 3. Perform asserts
 * 4. Navigate to compare page(click "compare product" link at the top of the page)
 * 5. Perform all asserts
 *
 * @group Compare_Products_(MX)
 * @ZephyrId MAGETWO-25843
 */
class TestCreationForAddingCompareProductsTest extends Injectable
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
     * Injection data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogProductCompare $catalogProductCompare
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogProductCompare $catalogProductCompare
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogProductCompare = $catalogProductCompare;
    }

    /**
     * Test creation for adding compare products
     *
     * @param FixtureFactory $fixtureFactory
     * @param CustomerAccountLogin $customerAccountLogin
     * @param string $products
     * @param CustomerInjectable $customer
     * @param string $customerIsLogin
     * @param Price $price
     * @param CatalogProductView $catalogProductView
     * @param AssertProductCompareSuccessAddedMessage $assertProductCompareSuccessAddedMessage
     * @return array
     */
    public function test(
        FixtureFactory $fixtureFactory,
        CustomerAccountLogin $customerAccountLogin,
        $products,
        CustomerInjectable $customer,
        $customerIsLogin,
        Price $price,
        CatalogProductView $catalogProductView,
        AssertProductCompareSuccessAddedMessage $assertProductCompareSuccessAddedMessage
    ) {
        //Steps
        if ($customerIsLogin == 'Yes') {
            $customer->persist();
            $this->cmsIndex->open();
            if (!$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
                $this->cmsIndex->getLinksBlock()->openLink("Log In");
                $customerAccountLogin->getLoginBlock()->login($customer);
            }
        }

        $products = explode(',', $products);
        foreach ($products as &$product) {
            list($fixture, $dataSet) = explode('::', $product);
            $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
            $product->persist();
        }

        foreach ($products as $itemProduct) {
            $catalogProductView->init($itemProduct);
            $catalogProductView->open();
            $catalogProductView->getViewBlock()->clickAddToCompare();
            $assertProductCompareSuccessAddedMessage->configure(
                $this,
                ['catalogProductView' => $catalogProductView, 'product' => $itemProduct]
            );
            \PHPUnit_Framework_Assert::assertThat($this->getName(), $assertProductCompareSuccessAddedMessage);
        }
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        $productsPrice = $price->getPreset();
        $this->products = $products;

        return ['products' => $products, 'productsPrice' => $productsPrice];
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        // TODO after fix bug MAGETWO-22756 delete first step
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->cmsIndex->getLinksBlock()->openLink("Compare Products");
        for ($i = 1; $i <= count($this->products); $i++) {
            $this->catalogProductCompare->getCompareProductsBlock()->removeProduct();
        }
    }
}
