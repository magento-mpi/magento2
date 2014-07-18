<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\GiftCardAccount\Test\Page\CheckoutCart;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class AddProductsToTheCart
 * Adding created products to the cart
 */
class AddProductsToTheCart implements StepInterface
{
    /**
     * Array with products
     *
     * @var array
     */
    protected $products;

    /**
     * Frontend product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * Page of checkout page
     *
     * @var CheckoutCart
     */
    protected $checkoutCart;

    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CmsIndex $cmsIndex
     * @param array $productsHolder
     */
    public function __construct(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CmsIndex $cmsIndex,
        array $productsHolder
    ) {
        $this->products = $productsHolder;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
    }

    /**
     * Run step that adding product to the cart
     *
     * @return void
     */
    public function run()
    {
        $productQuantity = $this->cmsIndex->getCartSidebarBlock()->getQuantity();
        if ($productQuantity != '0') {
            $this->checkoutCart->open();
            $this->checkoutCart->getCartBlock()->clearShoppingCart();
        }

        foreach ($this->products as $product) {
            $this->catalogProductView->init($product);
            $this->catalogProductView->open();
            $this->catalogProductView->getViewBlock()->clickAddToCart();
        }
    }
}
