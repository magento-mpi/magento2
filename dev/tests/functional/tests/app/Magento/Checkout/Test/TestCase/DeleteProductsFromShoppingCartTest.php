<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Checkout\Test\TestCase;

use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class DeleteProductsFromShoppingCartTest
 * Test delete products from shopping cart
 *
 * Preconditions
 * 1. Test products are created
 *
 * Steps:
 * 1. Add product(s) to Shopping Cart
 * 2. Click 'Remove item' button from Shopping Cart for each product(s)
 * 3. Perform all asserts
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-25218
 */
class DeleteProductsFromShoppingCartTest extends Injectable
{
    /**
     * Browser interface
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Catalog product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Checkout cart page
     *
     * @var CheckoutCart
     */
    protected $cartPage;

    /**
     * Prepare test data
     *
     * @param Browser $browser
     * @param FixtureFactory $fixtureFactory
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $cartPage
     * @return array
     */
    public function __prepare(
        Browser $browser,
        FixtureFactory $fixtureFactory,
        CatalogProductView $catalogProductView,
        CheckoutCart $cartPage
    ) {
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
        $this->catalogProductView = $catalogProductView;
        $this->cartPage = $cartPage;
    }

    /**
     * Run test add products to shopping cart
     *
     * @param string $productsData
     * @return void
     */
    public function test($productsData)
    {
        /** @var CatalogProductSimple[] $products */
        $products = $this->prepareProducts($productsData);

        foreach ($products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->addToCart($product);
        }

        foreach ($products as $product) {
            $this->cartPage->getCartBlock()->getCartItem($product)->removeItem();
        }
    }

    /**
     * Create products
     *
     * @param string $productList
     * @return InjectableFixture[]
     */
    protected function prepareProducts($productList)
    {
        $productsData = explode(',', $productList);
        $products = [];

        foreach ($productsData as $productConfig) {
            list($fixtureClass, $dataSet) = explode('::', trim($productConfig));
            $product = $this->fixtureFactory->createByCode($fixtureClass, ['dataSet' => $dataSet]);
            $product->persist();
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Clear shopping cart after test
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->cartPage->open();
        $this->cartPage->getCartBlock()->clearShoppingCart();
    }
}
