<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage; 

/**
 * Class CatalogProductNew
 *
 * @package Magento\Catalog\Test\Page\Adminhtml
 */
class CatalogProductNew extends BackendPage
{
    const MCA = 'catalog/product/new';

    protected $_blocks = [
        'productForm' => [
            'name' => 'productForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'productPageAction' => [
            'name' => 'productPageAction',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\ProductPageAction',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'productAttributeForm' => [
            'name' => 'productAttributeForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\AttributeForm',
            'locator' => '#create_new_attribute_container',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getProductForm()
    {
        return $this->getBlockInstance('productForm');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\ProductPageAction
     */
    public function getProductPageAction()
    {
        return $this->getBlockInstance('productPageAction');
    }
}
