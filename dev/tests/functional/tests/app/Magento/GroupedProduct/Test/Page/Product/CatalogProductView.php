<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Page\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductView as ParentCatalogProductView;

/**
 * Class CatalogProductView
 * Frontend grouped product view page
 */
class CatalogProductView extends ParentCatalogProductView
{
    const MCA = 'grouped/catalog/product/view';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['groupedViewBlock'] = [
            'name' => 'groupedViewBlock',
            'class' => 'Magento\GroupedProduct\Test\Block\Catalog\Product\View',
            'locator' => '.product.info.main',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }

    /**
     * @return \Magento\GroupedProduct\Test\Block\Catalog\Product\View
     */
    public function getGroupedViewBlock()
    {
        return $this->getBlockInstance('groupedViewBlock');
    }
}
