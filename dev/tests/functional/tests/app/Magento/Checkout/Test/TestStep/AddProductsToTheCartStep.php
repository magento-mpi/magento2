<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestStep;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Client\Browser;
use Mtf\TestStep\TestStepInterface;

/**
 * Class AddProductsToTheCartStep
 * Adding created products to the cart
 */
class AddProductsToTheCartStep implements TestStepInterface
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
     * Interface Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * @constructor
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param CmsIndex $cmsIndex
     * @param Browser $browser
     * @param array $products
     */
    public function __construct(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        CmsIndex $cmsIndex,
        Browser $browser,
        array $products
    ) {
        $this->products = $products;
        $this->catalogProductView = $catalogProductView;
        $this->checkoutCart = $checkoutCart;
        $this->cmsIndex = $cmsIndex;
        $this->browser = $browser;
    }

    /**
     * Add products to the cart
     *
     * @return void
     */
    public function run()
    {
        // Ensure that shopping cart is empty
        $this->checkoutCart->open()->getCartBlock()->clearShoppingCart();

        foreach ($this->products as $product) {
            $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            $this->catalogProductView->getViewBlock()->addToCart($product);
        }
    }
}
