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
use Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations\Variations;
use Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations\VariationsForm;
use Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations\AffectedAttributeSetChooser;
use Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations\CurrentVariations;

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
     * Add variations block
     *
     * @var Variations
     */
    private $variationsBlock;

    /**
     * Add variations form
     *
     * @var VariationsForm
     */
    private $variationsForm;

    /**
     * Choose attribute set in pop-up
     *
     * @var AffectedAttributeSetChooser
     */
    private $attributeSetChoice;

    /**
     * Add current variation block
     *
     * @var CurrentVariations
     */
    private $currentVariations;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;

        $this->productFormBlock = Factory::getBlockFactory()->getMagentoCatalogBackendProductForm(
            $this->_browser->find('body', Locator::SELECTOR_CSS)
        );
        $this->variationsBlock = Factory::getBlockFactory()->
            getMagentoCatalogProductConfigurableTabVariationsVariations(
                $this->_browser->find('#product_info_tabs_super_config_content'));
        $this->variationsForm = Factory::getBlockFactory()->
            getMagentoCatalogProductConfigurableTabVariationsVariationsForm(
                $this->_browser->find('#configurable-attributes-container'));
        $this->attributeSetChoice = Factory::getBlockFactory()->
            getMagentoCatalogProductConfigurableTabVariationsAffectedAttributeSetChooser(
                $this->_browser->find(
                    "//*[@id='affected-attribute-set-form']/ancestor::*"
                    . "[contains(concat(' ', normalize-space(@class), ' '), ' ui-dialog ')]",
                    Locator::SELECTOR_XPATH
                )
            );
        $this->currentVariations = Factory::getBlockFactory()->
            getMagentoCatalogProductConfigurableTabVariationsCurrentVariations(
                $this->_browser->find('#product-variations-matrix'));
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

    /**
     * Get the backend catalog variations block
     *
     * @return Variations
     */
    public function getVariationsBlock()
    {
        return $this->variationsBlock;
    }

    /**
     * Get the backend catalog variations form
     *
     * @return VariationsForm
     */
    public function getVariationsForm()
    {
        return $this->variationsForm;
    }

    /**
     * Get the backend attribute set chooser popup
     *
     * @return AffectedAttributeSetChooser
     */
    public function getAffectedAttributeSetChooser()
    {
        return $this->attributeSetChoice;
    }

    /**
     * Get the backend catalog current variations block
     *
     * @return CurrentVariations
     */
    public function getCurrentVariations()
    {
        return $this->currentVariations;
    }
}
