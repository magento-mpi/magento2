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
        'wishlistBlock' => [
            'name' => 'wishlistBlock',
            'class' => 'Magento\Wishlist\Test\Block\Customer\Wishlist',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Wishlist\Test\Block\Customer\Wishlist
     */
    public function getWishlistBlock()
    {
        return $this->getBlockInstance('wishlistBlock');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
