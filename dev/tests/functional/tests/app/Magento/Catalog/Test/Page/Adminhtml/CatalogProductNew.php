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
 */
class CatalogProductNew extends BackendPage
{
    const MCA = 'catalog/product/new';

    protected $_blocks = [
        'form' => [
            'name' => 'form',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\ProductForm',
            'locator' => '[id="page:main-container"]',
            'strategy' => 'css selector',
        ],
        'formAction' => [
            'name' => 'formAction',
            'class' => 'Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
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
     * Page initialization
     *
     * @param FixtureInterface $fixture
     * @return void
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
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\ProductForm
     */
    public function getForm()
    {
        return $this->getBlockInstance('form');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\FormPageActions
     */
    public function getFormAction()
    {
        return $this->getBlockInstance('formAction');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
