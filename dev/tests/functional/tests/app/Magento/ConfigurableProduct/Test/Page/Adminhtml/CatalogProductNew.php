<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

/**
 * Class CatalogProductNew
 * Product new page(backend)
 */
class CatalogProductNew extends \Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew
{
    const MCA = 'configurable/catalog/product/new';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_blocks['formPageActions'] = [
            'name' => 'formPageActions',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ];
        $this->_blocks['productForm'] = [
            'name' => 'productForm',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
    }
}
