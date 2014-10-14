<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Items;

use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;

/**
 * Class Product
 * Wishlist item product form
 */
class Product extends \Magento\Wishlist\Test\Block\Customer\Wishlist\Items\Product
{
    /**
     * Product move to wishlist dropdown
     *
     * @var string
     */
    protected $moveToWishlist = '.move [data-toggle="dropdown"]';

    /**
     * Product move to wishlist dropdown items
     *
     * @var string
     */
    protected $moveToWishlistItem = 'span[title="%s"]';

    /**
     * Move product to wishlist
     *
     * @param MultipleWishlist $wishlist
     * @return void
     */
    public function moveToWishlist(MultipleWishlist $wishlist)
    {
        $this->_rootElement->find($this->moveToWishlist)->click();
        $this->_rootElement->find(sprintf($this->moveToWishlistItem, $wishlist->getName()))->click();
    }
}
