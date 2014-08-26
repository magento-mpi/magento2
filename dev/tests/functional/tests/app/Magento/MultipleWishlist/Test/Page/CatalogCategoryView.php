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
 * Class CatalogCategoryView
 */
class CatalogCategoryView extends FrontendPage
{
    const MCA = 'wishlist/catalog/category/view';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'wishlistSearchBlock' => [
            'class' => 'Magento\MultipleWishlist\Test\Block\Widget\Search',
            'locator' => '.widget.block.wishlist.find',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Widget\Search
     */
    public function getWishlistSearchBlock()
    {
        return $this->getBlockInstance('wishlistSearchBlock');
    }
}
