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
 * Class CatalogProductEdit
 *
 * @package Magento\Catalog\Test\Page\Adminhtml
 */
class CatalogProductEdit extends BackendPage
{
    const MCA = 'catalog/product/edit';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Form',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'productForm' => [
            'name' => 'productForm',
            'class' => 'Magento\Catalog\Test\Block\Backend\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'configurableProductForm' => [
            'name' => 'configurableProductForm',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formAction' => [
            'name' => 'formAction',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\FormAction',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Backend\ProductForm
     */
    public function getProductForm()
    {
        return $this->getBlockInstance('productForm');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Form
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getConfigurableProductForm()
    {
        return $this->getBlockInstance('configurableProductForm');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\FormAction
     */
    public function getFormAction()
    {
        return $this->getBlockInstance('formAction');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }
}
