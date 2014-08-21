<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Product;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Product Multiple Wish list view block on the product page
 */
class View extends Block
{
    /**
     * Add to Multiple Wishlist button
     *
     * @var string
     */
    protected $addToMultipleWishlist = 'button[aria-haspopup="true"]';

    /**
     * Item wish list
     *
     * @var string
     */
    protected $wishlistItem = '//span[.="%s"]';

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
