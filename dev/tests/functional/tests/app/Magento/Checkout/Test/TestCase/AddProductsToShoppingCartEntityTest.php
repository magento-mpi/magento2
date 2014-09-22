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
use Mtf\Fixture\FixtureFactory;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\GiftCard\Test\Fixture\GiftCardProduct;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CatalogSearch\Test\Fixture\CatalogSearchQuery;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;

/**
 * Test Creation for AddProductsToShoppingCartEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. All type products is created
 *
 * Steps:
 * 1. Navigate to frontend
 * 2. Open test product page
 * 3. Add to cart test product
 * 4. Perform all asserts
 *
 * @group Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-25382
 */
class AddProductsToShoppingCartEntityTest extends Injectable
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
     * @param array $cart
     * @return array
     */
    public function test($productsData, array $cart)
    {
        $products = $this->prepareProducts($productsData);

        foreach ($products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->addToCart($product);
        }

        $cart['data']['items'] = ['products' => $products];
        return ['cart' => $this->fixtureFactory->createByCode('cart', $cart)];
    }

    /**
     * Create products
     *
     * @param string $productList
     * @return array
     */
    protected function prepareProducts($productList)
    {
        $productsData = explode(', ', $productList);
        $products = [];

        foreach ($productsData as $productConfig) {
            list($fixtureClass, $dataSet) = explode('::', $productConfig);
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
