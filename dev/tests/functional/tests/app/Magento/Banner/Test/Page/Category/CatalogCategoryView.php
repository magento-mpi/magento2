<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Page\Category;

use Magento\Catalog\Test\Page\Category\CatalogCategoryView as AbstractCatalogCategoryView;

/**
 * Class CatalogCategoryView
 * Catalog Category page
 */
class CatalogCategoryView extends AbstractCatalogCategoryView
{
    const MCA = 'catalog/category/view';

    protected $_blocks = [
        'listProductBlock' => [
            'name' => 'listProductBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ListProduct',
            'locator' => '.products.wrapper.grid',
            'strategy' => 'css selector',
        ],
        'mapBlock' => [
            'name' => 'mapBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\Price',
            'locator' => '#map-popup-content',
            'strategy' => 'css selector',
        ],
        'layeredNavigationBlock' => [
            'name' => 'layeredNavigationBlock',
            'class' => 'Magento\LayeredNavigation\Test\Block\Navigation',
            'locator' => '.block.filter',
            'strategy' => 'css selector',
        ],
        'toolbar' => [
            'name' => 'toolbar',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Toolbar',
            'locator' => '.toolbar.products',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title h1.title .base',
            'strategy' => 'css selector',
        ],
        'viewBlock' => [
            'name' => 'viewBlock',
            'class' => 'Magento\Banner\Test\Block\Category\View',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Category\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }
}
