<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

/**
 * Class CatalogProductEdit
 * Product edit page(backend)
 */
class CatalogProductEdit extends \Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit
{
    const MCA = 'configurable/product/edit';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_blocks['productForm'] = [
            'name' => 'productForm',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
    }
}
