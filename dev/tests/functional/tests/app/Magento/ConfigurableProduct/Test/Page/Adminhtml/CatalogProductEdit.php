<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit as ParentCatalogProductEdit;

/**
 * Class CatalogProductEdit
 */
class CatalogProductEdit extends ParentCatalogProductEdit
{
    const MCA = 'configurable/catalog/product/edit';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_blocks['form'] = [
            'name' => 'form',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
