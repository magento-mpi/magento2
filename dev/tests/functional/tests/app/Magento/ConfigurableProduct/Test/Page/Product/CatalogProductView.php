<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductView as AbstractCatalogProductView;

class CatalogProductView extends AbstractCatalogProductView
{
    /**
     * Init page. Set page url
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

    /**
     * Get product view block
     *
     * @return \Magento\ConfigurableProduct\Test\Block\Product\View
     */
    public function getViewBlock()
    {
        return $this->getBlockInstance('viewBlock');
    }
}
