<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Page;


/**
 * Class WishlistIndex
 */
class WishlistIndex extends \Magento\Wishlist\Test\Page\WishlistIndex
{
    const MCA = 'giftregistry/wishlist/index';

    /**
     * Initialize blocks
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['wishlistBlock'] = [
            'name' => 'wishlistBlock',
            'class' => 'Magento\GiftRegistry\Test\Block\Customer\Wishlist',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\GiftRegistry\Test\Block\Customer\Wishlist
     */
    public function getWishlistBlock()
    {
        return $this->getBlockInstance('wishlistBlock');
    }
}
