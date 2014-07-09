<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Page\Product;

use Magento\Catalog\Test\Page\Product\CatalogProductView as ParentCatalogProductView;

/**
 * Class CatalogProductView
 * Frontend bundle product view page
 */
class CatalogProductView extends ParentCatalogProductView
{
    const MCA = 'bundle/catalog/product/view';

    /**
     * Custom constructor
     *
     * @return void
     */
    protected function _init()
    {
        $this->_blocks['bundleViewBlock'] = [
            'name' => 'bundleViewBlock',
            'class' => 'Magento\Bundle\Test\Block\Catalog\Product\View',
            'locator' => '.bundle.options.container',
            'strategy' => 'css selector',
        ];
        parent::_init();
    }

    /**
     * Bundle block on frontend
     *
     * @return \Magento\Bundle\Test\Block\Catalog\Product\View
     */
    public function getBundleViewBlock()
    {
        return $this->getBlockInstance('bundleViewBlock');
    }
}
