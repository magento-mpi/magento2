<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\TestStep;

use Mtf\Client\Browser;
use Mtf\Fixture\InjectableFixture;
use Mtf\TestStep\TestStepInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Class AddProductToMultipleWishlistStep
 * Add product to multiple wishlist
 */
class AddProductToMultipleWishlistStep implements TestStepInterface
{
    /**
     * Injectable fixture
     *
     * @var InjectableFixture
     */
    protected $product;

    /**
     * Browser
     *
     * @var Browser
     */
    protected $browser;

    /**
     * CatalogProductView page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * MultipleWishlist fixture
     *
     * @var MultipleWishlist
     */
    protected $multipleWishlist;

    /**
     * @var string
     */
    protected $duplicate;

    /**
     * @constructor
     * @param InjectableFixture $product
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param MultipleWishlist $multipleWishlist
     * @param string $duplicate
     */
    public function __construct(
        InjectableFixture $product,
        Browser $browser,
        CatalogProductView $catalogProductView,
        MultipleWishlist $multipleWishlist,
        $duplicate
    ) {
        $this->product = $product;
        $this->browser = $browser;
        $this->duplicate = $duplicate;
        $this->catalogProductView = $catalogProductView;
        $this->multipleWishlist = $multipleWishlist;
    }

    /**
     * Add product to multiple wishlist
     *
     * @return void
     */
    public function run()
    {
        $this->browser->open($_ENV['app_frontend_url'] . $this->product->getUrlKey() . '.html');
        $this->catalogProductView->getViewBlock()->fillOptions($this->product);
        $checkoutData = $this->product->getCheckoutData();
        if (isset($checkoutData['qty'])) {
            $qty = $this->duplicate === 'yes'
                ? $checkoutData['qty'] / 2
                : $checkoutData['qty'];
            $this->catalogProductView->getViewBlock()->setQty($qty);
        }
        $this->catalogProductView->getMultipleWishlistViewBlock()->addToMultipleWishlist($this->multipleWishlist);
    }
}
