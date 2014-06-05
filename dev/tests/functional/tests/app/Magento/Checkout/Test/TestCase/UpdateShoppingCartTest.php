<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Test Creation for Update ShoppingCart
 *
 * Test Flow:
 * Precondition:
 * 1. Simple product is created
 * 2. Clear shopping cart
 *
 * Steps:
 * 1. Go to frontend
 * 2. Add product with qty = 1 to shopping cart
 * 3. Fill in all data according to data set
 * 4. Click "Update Shopping Cart" button
 * 5. Perform all assertion from dataset
 *
 * @group Shopping Cart (CS)
 * @ZephyrId MAGETWO-25081
 */
class UpdateShoppingCartTest extends Injectable
{
    /**
     * Product for update shopping cart
     *
     * @var CatalogProductSimple
     */
    protected $productForUpdateShoppingCart;

    /**
     * Page CmsIndex
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Page CatalogCategoryView
     *
     * @var CatalogCategoryView
     */
    protected $catalogCategoryView;

    /**
     * Page CatalogProductView
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page CheckoutCart
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Inject data
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function __inject(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->catalogCategoryView = $catalogCategoryView;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
    }

    /**
     * Create simple product with price "100" and category
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $this->productForUpdateShoppingCart = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $this->productForUpdateShoppingCart->persist();

        return [
            'productName' => $this->productForUpdateShoppingCart->getName()
        ];
    }

    /**
     * Update Shopping Cart
     *
     * @param Cart $cart
     * @return void
     */
    public function testUpdateShoppingCart(Cart $cart)
    {
        // Preconditions
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();
        $categoryName = $this->productForUpdateShoppingCart->getCategoryIds()[0]['name'];
        $productName = $this->productForUpdateShoppingCart->getName();

        // Steps
        $this->cmsIndex->open();
        $this->cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $this->catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $this->catalogProductView->getViewBlock()->clickAddToCart();
        $this->checkoutCart->getCartBlock()->setProductQty($cart->getQty());
        $this->checkoutCart->getCartBlock()->updateShoppingCart();
    }
}
