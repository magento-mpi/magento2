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
 * Class WishlistShare
 */
class WishlistShare extends FrontendPage
{
    const MCA = 'wishlist/index/share';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'sharingInfoForm' => [
            'class' => 'Magento\Wishlist\Test\Block\Customer\Sharing',
            'locator' => '.wishlist.share',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Wishlist\Test\Block\Customer\Sharing
     */
    public function getSharingInfoForm()
    {
        return $this->getBlockInstance('sharingInfoForm');
    }
}
