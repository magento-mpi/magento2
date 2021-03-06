<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\TestStep;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Mtf\Client\Browser;
use Mtf\Fixture\InjectableFixture;
use Mtf\TestStep\TestStepInterface;

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
     * Variable that determines whether to add product to wish list for the second time or not
     *
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
     * Add product to multiple wish list
     *
     * @return void
     */
    public function run()
    {
        $this->addToMultipleWishlist();
        if ($this->duplicate == 'yes') {
            $this->addToMultipleWishlist();
        }
    }

    /**
     * Add product to multiple wish list
     *
     * @return void
     */
    protected function addToMultipleWishlist()
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
