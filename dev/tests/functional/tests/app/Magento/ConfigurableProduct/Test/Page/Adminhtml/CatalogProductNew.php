<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew as ParentCatalogProductNew;

/**
 * Class CatalogProductNew
 */
class CatalogProductNew extends ParentCatalogProductNew
{
    const MCA = 'configurable/catalog/product/new';

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
