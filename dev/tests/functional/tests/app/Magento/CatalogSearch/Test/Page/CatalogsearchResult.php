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
        'toolbar' => [
            'name' => 'toolbar',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\Toolbar',
            'locator' => '.toolbar.products',
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
     * @return \Magento\Catalog\Test\Block\Product\ProductList\Toolbar
     */
    public function getToolbar()
    {
        return $this->getBlockInstance('toolbar');
    }
}
