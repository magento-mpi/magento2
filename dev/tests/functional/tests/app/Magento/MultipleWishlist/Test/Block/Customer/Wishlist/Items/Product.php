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
 * Wish list item product form.
 */
class Product extends \Magento\Wishlist\Test\Block\Customer\Wishlist\Items\Product
{
    /**
     * Product action to wish list drop-down.
     *
     * @var string
     */
    protected $typeActionWishlist = '.%s [data-toggle="dropdown"]';

    /**
     * Product move to wish list drop-down items.
     *
     * @var string
     */
    protected $wishlistItem = 'div.%s span[title="%s"]';

    /**
     * Action product to wish list.
     *
     * @param MultipleWishlist $wishlist
     * @param string $action
     * @return void
     */
    public function actionToWishlist(MultipleWishlist $wishlist, $action)
    {
        $this->_rootElement->find(sprintf($this->typeActionWishlist, $action))->click();
        $this->_rootElement->find(sprintf($this->wishlistItem, $action, $wishlist->getName()))->click();
    }
}
