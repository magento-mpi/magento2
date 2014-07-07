<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Page\Adminhtml;

use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit as ParentCatalogProductEdit;

/**
 * Class CatalogProductEdit
 */
class CatalogProductEdit extends ParentCatalogProductEdit
{
    const MCA = 'catalog/product_downloadable/edit';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_blocks['form'] = [
            'name' => 'form',
            'class' => 'Magento\Downloadable\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
    }

    /**
     * @return \Magento\Downloadable\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
