<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class CatalogProductNew
 * Product new page(backend)
 */
class CatalogProductNew extends BackendPage
{
    const MCA = 'configurable/catalog/product/new';

    protected $_blocks = [
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages .messages',
            'strategy' => 'css selector',
        ],
        'formPageActions' => [
            'name' => 'formPageactions',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'form' => [
            'name' => 'form',
            'class' => 'Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\FormPAgeActions
     */
    public function getFormPageActions()
    {
        return $this->getBlockInstance('formPageActions');
    }

    /**
     * @return \Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }
}
