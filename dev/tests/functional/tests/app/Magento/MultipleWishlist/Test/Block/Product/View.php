<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Product;

use Mtf\Client\Element\Locator;

/**
 * Class View
 * Product Multiple Wish list view block on the product page
 */
class View extends \Magento\Catalog\Test\Block\Product\View
{
    /**
     * Add to Multiple Wishlist button
     *
     * @var string
     */
    protected $addToMultipleWishlist = '.wishlist [data-toggle="dropdown"]';

    /**
     * Item wish list
     *
     * @var string
     */
    protected $wishlistItem = '//*[@data-action="add-to-wishlist" and @title = "%s"]';

    /**
     * Select which Wishlist you want to add product to
     *
     * @param string $wishlistName
     * @return void
     */
    public function addToMultipleWishlist($wishlistName)
    {
        $this->_rootElement->find($this->addToMultipleWishlist)->click();
        $this->_rootElement->find(sprintf($this->wishlistItem, $wishlistName), Locator::SELECTOR_XPATH)->click();
    }
}
