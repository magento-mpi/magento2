<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CatalogsearchResult
 */
class CatalogsearchResult extends FrontendPage
{
    const MCA = 'catalogsearch/result';

    protected $_blocks = [
        'listProductBlock' => [
            'name' => 'listProductBlock',
            'class' => 'Magento\Catalog\Test\Block\Product\ListProduct',
            'locator' => '.search.results',
            'strategy' => 'css selector',
        ],
        'bottomToolbar' => [
            'name' => 'bottomToolbar',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\BottomToolbar',
            'locator' => './/*[contains(@class,"toolbar-products")][2]',
            'strategy' => 'xpath',
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
     * @return \Magento\Catalog\Test\Block\Product\ProductList\BottomToolbar
     */
    public function getBottomToolbar()
    {
        return $this->getBlockInstance('bottomToolbar');
    }
}
