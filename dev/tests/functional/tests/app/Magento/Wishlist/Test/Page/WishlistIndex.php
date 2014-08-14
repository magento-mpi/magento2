<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class WishlistIndex
 */
class WishlistIndex extends FrontendPage
{
    const MCA = 'wishlist/index/index';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages',
            'strategy' => 'css selector',
        ],
        'wishlistBlock' => [
            'name' => 'wishlistBlock',
            'class' => 'Magento\Wishlist\Test\Block\Customer\Wishlist',
            'locator' => '#wishlist-view-form',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\Wishlist\Test\Block\Customer\Wishlist
     */
    public function getWishlistBlock()
    {
        return $this->getBlockInstance('wishlistBlock');
    }
}
