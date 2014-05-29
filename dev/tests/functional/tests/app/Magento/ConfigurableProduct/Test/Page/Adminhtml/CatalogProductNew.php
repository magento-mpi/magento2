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
    const MCA = 'catalog/product_configurable/new';
    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_blocks['form'] = [
            'name' => 'form',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ];
        $this->_url = $_ENV['app_backend_url'] . static::MCA;
    }

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Form
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
