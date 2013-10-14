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

namespace Magento\Catalog\Test\Page;

use Magento\Catalog\Test\Block\Backend\ProductForm;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Page\Page;

/**
 * Class AdminCatalogProductNew
 * Create product page
 *
 * @package Magento\Catalog\Test\Page\Catalog\Product
 */
class AdminCatalogProductNew extends Page
{
    /**
     * URL for product creation
     */
    const MCA = 'admin/catalog_product/new';

    /**
     * @var ProductForm
     */
    private $productFormBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->productFormBlock = Factory::getBlockFactory()->getMagentoCatalogBackendProductForm(
            $this->_browser->find('body', Locator::SELECTOR_CSS)
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
     * Assert result
     *
     * @param DataFixture $fixture
     * @return mixed
     */
    public function assertProductSaveResult(DataFixture $fixture)
    {
        $dataConfig = $fixture->getDataConfig();
        $method = 'waitForProductSave' . $dataConfig['constraint'];
        return $this->$method($fixture);
    }

    /**
     * Check for success message
     *
     * @param DataFixture $fixture
     * @return bool
     */
    protected function waitForProductSaveSuccess(DataFixture $fixture)
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-success"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * Check for error message
     *
     * @param DataFixture $fixture
     * @return bool
     */
    protected function waitForProductSaveError(DataFixture $fixture)
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-error"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $productSavedMessage = $browser->find($selector, $strategy);
                return $productSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * Check for Java Script error message
     *
     * @param DataFixture $fixture
     * @return mixed
     */
    protected function waitForProductSaveJavascriptError(DataFixture $fixture)
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
