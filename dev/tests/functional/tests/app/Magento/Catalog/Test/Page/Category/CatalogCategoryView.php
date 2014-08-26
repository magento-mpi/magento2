<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Category;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogCategoryView
 */
class CatalogCategoryView extends FrontendPage
{
    const MCA = 'catalog/category/view';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'bannerViewBlock' => [
            'class' => 'Magento\Banner\Test\Block\Category\View',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'listProductBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ListProduct',
            'locator' => '.products.wrapper.grid',
            'strategy' => 'css selector',
        ],
        'mapBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\Price',
            'locator' => '#map-popup-content',
            'strategy' => 'css selector',
        ],
        'layeredNavigationBlock' => [
            'class' => 'Magento\LayeredNavigation\Test\Block\Navigation',
            'locator' => '.block.filter',
            'strategy' => 'css selector',
        ],
        'toolbar' => [
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Toolbar',
            'locator' => '.toolbar.products',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title h1.title .base',
            'strategy' => 'css selector',
        ],
        'viewBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Category\View',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'eventBlock' => [
            'class' => 'Magento\CatalogEvent\Test\Block\Catalog\Event',
            'locator' => '.top-container',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Banner\Test\Block\Category\View
     */
    public function getBannerViewBlock()
    {
        return $this->getBlockInstance('bannerViewBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ListProduct
     */
    public function getListProductBlock()
    {
        return $this->getBlockInstance('listProductBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\Price
     */
    public function getMapBlock()
    {
        return $this->getBlockInstance('mapBlock');
    }

    /**
     * @return \Magento\LayeredNavigation\Test\Block\Navigation
     */
    public function getLayeredNavigationBlock()
    {
        return $this->getBlockInstance('layeredNavigationBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Toolbar
     */
    public function getToolbar()
    {
        return $this->getBlockInstance('toolbar');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Category\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }

    /**
     * @return \Magento\CatalogEvent\Test\Block\Catalog\Event
     */
    public function getEventBlock()
    {
        return $this->getBlockInstance('eventBlock');
    }
}
