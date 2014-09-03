<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Page;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView as ParentCatalogCategoryView;

/**
 * Class CatalogCategoryView
 * Catalog Category page
 */
class CatalogCategoryView extends ParentCatalogCategoryView
{
    const MCA = 'wishlist/catalog/category/view';

    /**
     * Init page
     *
     * @return void
     */
    public function _init()
    {
        $this->_blocks += [
            'wishlistSearchBlock' => [
                'name' => 'wishlistSearchBlock',
                'class' => 'Magento\MultipleWishlist\Test\Block\Widget\Search',
                'locator' => '.widget.block.wishlist.find',
                'strategy' => 'css selector',
            ],
        ];
        parent::_init();
    }

    /**
     * @return \Magento\MultipleWishlist\Test\Block\Widget\Search
     */
    public function getWishlistSearchBlock()
    {
        return $this->getBlockInstance('wishlistSearchBlock');
    }
}
