<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Block\Customer\Wishlist;

use Mtf\Block\Block;

/**
 * Class Wishlist
 * Wish list block on 'My Wish List' page
 */
class Wishlist extends Block
{
    /**
     * Button "Create New Wish List" selector
     *
     * @var string
     */
    protected $addWishlist = '.action.add.wishlist';

    /**
     * Create new wish list
     *
     * @return void
     */
    protected function createWishlist()
    {
        $this->_rootElement->find($this->addWishlist)->click();
    }
}
