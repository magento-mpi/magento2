<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class MultipleWishlistIndex
 */
class MultipleWishlistIndex extends FrontendPage
{
    const MCA = 'wishlist/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'managementBlock' => [
            'class' => 'Magento\MultipleWishlist\Test\Block\Customer\Wishlist\Management',
            'locator' => '//*[*[contains(@class,"message notice") or contains(@class,"wishlist management")]]',
            'strategy' => 'xpath',
        ],
        'behaviourBlock' => [
            'class' => 'Magento\MultipleWishlist\Test\Block\Behaviour',
            'locator' => '[id$="wishlist-block"].popup.active',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages .messages',
            'strategy' => 'css selector',
        ],
    ];

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

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
