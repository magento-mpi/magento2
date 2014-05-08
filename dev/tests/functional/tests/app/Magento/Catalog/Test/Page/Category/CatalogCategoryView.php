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
            'class' => 'Magento\Search\Test\Block\Catalog\Layer\View',
            'locator' => '.block.filter',
            'strategy' => 'css selector',
        ],
        'productPagination' => [
            'name' => 'productPagination',
            'class' => 'Magento\Catalog\Test\Block\Category\ProductPagination',
            'locator' => '.pages .items',
            'strategy' => 'css selector',
        ],
    ];

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
     * @return \Magento\Search\Test\Block\Catalog\Layer\View
     */
    public function getLayeredNavigationBlock()
    {
        return $this->getBlockInstance('layeredNavigationBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Category\ProductPagination
     */
    public function getProductPagination()
    {
        return $this->getBlockInstance('productPagination');
    }
}
