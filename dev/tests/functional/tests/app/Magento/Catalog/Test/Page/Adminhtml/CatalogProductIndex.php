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
 * Class CatalogProductIndex
 * Page with Grid in Catalog
 */
class CatalogProductIndex extends BackendPage
{
    const MCA = 'catalog/product/index';

    protected $_blocks = [
        'productGrid' => [
            'name' => 'productGrid',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Grid',
            'locator' => '#productGrid',
            'strategy' => 'css selector',
        ],
        'messageBlock' => [
            'name' => 'messageBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
        'productBlock' => [
            'name' => 'productBlock',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product',
            'locator' => '#add_new_product',
            'strategy' => 'css selector',
        ],
        'accessDeniedBlock' => [
            'name' => 'accessDeniedBlock',
            'class' => 'Magento\Backend\Test\Block\Denied',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'addNewSplitButtonBlock' => [
            'name' => 'addNewSplitButtonBlock',
            'class' => 'Magento\Catalog\Test\Block\Backend\AddNewSplitButton',
            'locator' => '[id="add_new_product"]',
            'strategy' => 'css selector',
        ],
        'FormPageActions' => [
            'name' => 'GridPageActions',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions',
            'locator' => '#add_new_product',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Grid
     */
    public function getProductGrid()
    {
        return $this->getBlockInstance('productGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return $this->getBlockInstance('messageBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product
     */
    public function getProductBlock()
    {
        return $this->getBlockInstance('productBlock');
    }

    /**
     * @return \Magento\Backend\Test\Block\Denied
     */
    public function getAccessDeniedBlock()
    {
        return $this->getBlockInstance('accessDeniedBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Backend\AddNewSplitButton
     */
    public function getAddNewSpliteButtonBlock()
    {
        return $this->getBlockInstance('addNewSplitButtonBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('FormPageActions');
    }
}
