<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Page\Product;

/**
 * Class CatalogProductView
 * Frontend bundle product view page
 */
class CatalogProductView extends \Magento\Catalog\Test\Page\Product\CatalogProductView
{
    const MCA = 'bundle/catalog/product/view';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['viewBlock'] = [
            'name' => 'bundleViewBlock',
            'class' => 'Magento\Bundle\Test\Block\Catalog\Product\View',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }
}
