<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Page;

use Magento\Wishlist\Test\Page\WishlistIndex;

/**
 * Class WishlistIndex
 */
class MultipleWishlistIndex extends WishlistIndex
{
    const MCA = 'wishlist/index';

    /**
     * Init page
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks += [
            'managementBlock' => [
                'name' => 'managementBlock',
                'class' => 'Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Management',
                'locator' => '.column.main',
                'strategy' => 'css selector',
            ],
            'behaviourBlock' => [
                'name' => 'behaviourBlock',
                'class' => 'Magento\MultipleWishlist\Test\Block\Behaviour',
                'locator' => '#create-wishlist-block',
                'strategy' => 'css selector',
            ],
        ];
        parent::_init();
    }

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Management
     */
    public function getManagementBlock()
    {
        return $this->getBlockInstance('managementBlock');
    }

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Behaviour
     */
    public function getBehaviourBlock()
    {
        return $this->getBlockInstance('behaviourBlock');
    }
}
