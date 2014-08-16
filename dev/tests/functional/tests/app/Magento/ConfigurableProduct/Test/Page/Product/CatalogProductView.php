<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Product;

/**
 * Class CatalogProductView
 * Product view page on frontend
 */
class CatalogProductView extends \Magento\Catalog\Test\Page\Product\CatalogProductView
{
    const MCA = 'configurable/product/view';
    
    /**
     * Init page
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['viewBlock'] = [
            'name' => 'viewBlock',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ];
    }
}
