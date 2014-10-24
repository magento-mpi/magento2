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
use Mtf\Fixture\FixtureInterface;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Update Product from MiniShoppingCart
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create product according to dataSet.
 * 2. Go to frontend.
 * 3. Add products to cart.
 *
 * Steps:
 * 1. Click on mini shopping cart icon.
 * 2. Click Edit.
 * 3. Fill data from dataSet.
 * 4. Click Update.
 * 5. Perform all assertions.
 *
 * @group Mini_Shopping_Cart_(CS)
 * @ZephyrId MAGETWO-29812
 */
class UpdateProductFromMiniShoppingCartEntityTest extends Injectable
{
    /**
     * Browser interface.
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Catalog product view page.
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Cms index page.
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Inject data.
     *
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param FixtureFactory $fixtureFactory
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        Browser $browser,
        CatalogProductView $catalogProductView,
        FixtureFactory $fixtureFactory
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->browser = $browser;
        $this->catalogProductView = $catalogProductView;
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * Update product from mini shopping cart.
     *
     * @param string $originalProduct
     * @param array $checkoutData
     * @return array
     */
    public function test($originalProduct, $checkoutData)
    {
        // Preconditions:
        $product = $this->createProduct($originalProduct);
        $this->addToCart($product);

        // Steps:
        $newProduct = $this->createProduct(
            explode('::', $originalProduct)[0],
            [array_replace_recursive($product->getData(), ['checkout_data' => $checkoutData])]
        );
        $this->updateProductOnMiniShoppingCart($newProduct);

        // Prepare data for asserts:
        $cart['data']['items'] = ['products' => [$newProduct]];
        $deletedCart['data']['items'] = ['products' => [$product]];

        return [
            'deletedCart' => $this->fixtureFactory->createByCode('cart', $deletedCart),
            'cart' => $this->fixtureFactory->createByCode('cart', $cart)
        ];
    }

    /**
     * Create product.
     *
     * @param string $product
     * @param array $data [optional]
     * @return FixtureInterface
     */
    protected function createProduct($product, array $data = [])
    {
        $createProductsStep = $this->objectManager->create(
            'Magento\Catalog\Test\TestStep\CreateProductsStep',
            ['products' => $product, 'data' => $data]
        );
        return $createProductsStep->run()['products'][0];
    }

    /**
     * Add product to cart.
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function addToCart(FixtureInterface $product)
    {
        $addToCartStep = $this->objectManager->create(
            'Magento\Checkout\Test\TestStep\AddProductsToTheCartStep',
            ['products' => [$product]]
        );
        $addToCartStep->run();
    }

    /**
     * Update product on mini shopping cart.
     *
     * @param FixtureInterface $product
     * @return void
     */
    protected function updateProductOnMiniShoppingCart(FixtureInterface $product)
    {
        $miniShoppingCart = $this->cmsIndex->getCartSidebarBlock();
        $miniShoppingCart->openMiniCart();
        $miniShoppingCart->getCartItem($product)->clickEditItem();
        $this->catalogProductView->getViewBlock()->addToCart($product);
    }
}
