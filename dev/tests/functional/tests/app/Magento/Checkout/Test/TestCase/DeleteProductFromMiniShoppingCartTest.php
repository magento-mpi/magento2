<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\Fixture\FixtureInterface;
use Mtf\ObjectManager;
use Mtf\Client\Browser;
use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\InjectableFixture;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class DeleteProductFromMiniShoppingCartTest
 * Test delete products from shopping cart
 *
 * Preconditions
 * 1. Create product according to dataSet
 * 2. Add products to cart
 *
 * Steps:
 * 1. Open Frontend
 * 2. Click on mini shopping cart icon
 * 3. Click Delete
 * 4. Click Ok
 * 5. Perform all assertions
 *
 * @group  Mini Shopping Cart (CS)
 * @ZephyrId MAGETWO-29104
 */
class DeleteProductFromMiniShoppingCartTest extends Injectable
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
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

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
     * @param CmsIndex $cmsIndex
     * @param CheckoutCart $cartPage
     * @return void
     */
    public function __prepare(
        Browser $browser,
        FixtureFactory $fixtureFactory,
        CmsIndex $cmsIndex,
        CheckoutCart $cartPage
    ) {
        $this->browser = $browser;
        $this->fixtureFactory = $fixtureFactory;
        $this->cmsIndex = $cmsIndex;
        $this->cartPage = $cartPage;
    }

    /**
     * Run test add products to shopping cart
     *
     * @param string $products
     * @param int $deletedProductIndex
     * @return array
     */
    public function test($products, $deletedProductIndex)
    {
        // Preconditions
        $products = $this->prepareProducts($products);
        $this->cartPage->open();
        $this->cartPage->getCartBlock()->clearShoppingCart();

        // Steps
        $this->addToCart($products);
        $this->cartPage->getMessagesBlock()->waitSuccessMessage();
        $this->removeProduct($products[$deletedProductIndex], count($products));

        return ['products' => $products];
    }

    /**
     * Create products
     *
     * @param string $productList
     * @return InjectableFixture[]
     */
    protected function prepareProducts($productList)
    {
        $productsStep = ObjectManager::getInstance()->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $productList]
        );

        $result = $productsStep->run();
        return $result['products'];
    }

    /**
     * Add products to cart
     *
     * @param array $products
     * @return void
     */
    protected function addToCart(array $products)
    {
        $addToCartStep = ObjectManager::getInstance()->create(
            'Magento\Checkout\Test\TestStep\AddProductsToTheCartStep',
            ['products' => $products]
        );
        $addToCartStep->run();
    }

    /**
     * Remove product form cart
     *
     * @param FixtureInterface $product
     * @param int $countProducts
     * @return void
     */
    protected function removeProduct(FixtureInterface $product, $countProducts)
    {
        $this->cmsIndex->open();
        $this->cmsIndex->getCartSidebarBlock()->waitCounterQty();
        $this->cmsIndex->getCartSidebarBlock()->openMiniCart();
        $this->cmsIndex->getCartSidebarBlock()->getCartItem($product)->removeItemFromMiniCart();
        if ($countProducts > 1) {
            $this->cmsIndex->getCartSidebarBlock()->waitCounterQty();
        }
    }
}
