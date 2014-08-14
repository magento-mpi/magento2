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

    protected $_blocks = [
        'sharingInfoForm' => [
            'name' => 'sharingInfoForm',
            'class' => 'Magento\MultipleWishlist\Test\Block\Customer\Sharing',
            'locator' => '.wishlist.share',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Customer\Sharing
     */
    public function getSharingInfoForm()
    {
        return $this->getBlockInstance('sharingInfoForm');
    }
}
