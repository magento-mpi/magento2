<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Page;

/**
 * Class AdvancedResult
 */
class AdvancedResult extends CatalogsearchResult
{
    const MCA = 'catalogsearch/advanced/result';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['searchResultBlock'] = [
            'name' => 'searchResultBlock',
            'class' => 'Magento\CatalogSearch\Test\Block\Advanced\Result',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ];
        $this->_blocks['bottomToolbar'] = [
            'name' => 'bottomToolbar',
            'class' => 'Magento\Catalog\Test\Block\Product\ProductList\BottomToolbar',
            'locator' => './/*[contains(@class,"toolbar-products")][2]',
            'strategy' => 'xpath',
        ];
        parent::_init();
    }

    /**
     * @return \Magento\CatalogSearch\Test\Block\Advanced\Result
     */
    public function getSearchResultBlock()
    {
        return $this->getBlockInstance('searchResultBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\ProductList\BottomToolbar
     */
    public function getBottomToolbar()
    {
        return $this->getBlockInstance('bottomToolbar');
    }
}
