<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Adminhtml; 

use Mtf\Page\BackendPage;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CatalogProductNew
 *
 * @package Magento\Catalog\Test\Page\Adminhtml
 */
class CatalogProductNew extends BackendPage
{
    const MCA = 'catalog/product/new';

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
            'locator' => 'body',
            'strategy' => 'css selector',
        ],
        'productPageAction' => [
            'name' => 'productPageAction',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\ProductPageAction',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'affectedAttributeSetForm' => [
            'name' => 'affectedAttributeSetForm',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\AffectedAttributeSetForm',
            'locator' => '#affected-attribute-set-form',
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
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . static::MCA;
    }

    /**
     *
     * @param FixtureInterface $fixture
     */
    public function init(FixtureInterface $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $params = isset($dataConfig['create_url_params']) ? $dataConfig['create_url_params'] : array();
        foreach ($params as $paramName => $paramValue) {
            $this->_url .= '/' . $paramName . '/' . $paramValue;
        }
    }

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
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\ProductPageAction
     */
    public function getProductPageAction()
    {
        return $this->getBlockInstance('productPageAction');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\AffectedAttributeSetForm
     */
    public function getAffectedAttributeSetForm()
    {
        return $this->getBlockInstance('affectedAttributeSetForm');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }

    /**
     * Switch back to main page from iframe
     */
    public function switchToMainPage()
    {
        $this->_browser->switchToFrame();
    }
}
