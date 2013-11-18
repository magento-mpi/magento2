<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Page\Product;

use Mtf\Page\Page;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\Catalog\Test\Block\Backend\ProductForm;

/**
 * Class CatalogProductNew
 * Create product page
 *
 * @package Magento\Catalog\Test\Page\Product
 */
class CatalogProductNew extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'catalog/product/new';

    /**
     * @var ProductForm
     */
    private $productFormBlock;

    /**
     * Global messages block
     *
     * @var Messages
     */
    private $messagesBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->productFormBlock = Factory::getBlockFactory()->getMagentoCatalogBackendProductForm(
            $this->_browser->find('body', Locator::SELECTOR_CSS)
        );
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * @param DataFixture $fixture
     */
    public function init(DataFixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();

        $params = isset($dataConfig['create_url_params']) ? $dataConfig['create_url_params'] : array();
        foreach ($params as $paramName => $paramValue) {
            $this->_url .= '/' . $paramName . '/' . $paramValue;
        }
    }

    /**
     * Get product form block
     *
     * @return ProductForm
     */
    public function getProductBlockForm()
    {
        return $this->productFormBlock;
    }

    /**
     * Get global messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->messagesBlock;
    }

    /**
     * Check for Java Script error message
     *
     * @param DataFixture $fixture
     * @return mixed
     */
    protected function waitForProductSaveJavascriptError(DataFixture $fixture = null)
    {
        $browser = $this->_browser;
        $selector = '[class=mage-error]';
        $strategy = Locator::SELECTOR_CSS;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }
}
