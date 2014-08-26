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
 * Class SearchResult
 */
class SearchResult extends FrontendPage
{
    const MCA = 'wishlist/search/results';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'wishlistSearchResultBlock' => [
            'class' => 'Magento\MultipleWishlist\Test\Block\Widget\Search\Result',
            'locator' => '.block.wishlist.find.results',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Widget\Search\Result
     */
    public function getWishlistSearchResultBlock()
    {
        return $this->getBlockInstance('wishlistSearchResultBlock');
    }
}
