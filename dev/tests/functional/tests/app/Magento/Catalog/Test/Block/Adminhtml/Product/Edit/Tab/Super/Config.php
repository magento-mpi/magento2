<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Variations
 * Adminhtml catalog super product configurable tab
 *
 */
class Config extends Tab
{
    /**
     * 'Generate Variations' button
     *
     * @var string
     */
    protected $generateVariations = '[data-ui-id=product-variations-generator-generate]';

    /**
     * Product variations matrix block
     *
     * @var string
     */
    protected $matrixBlock = '[data-role=product-variations-matrix] table';

    /**
     * Product attribute block selector by attribute name
     *
     * @var string
     */
    protected $attribute = '//div[*/*/span="%s"]';

    /**
     * Magento loader
     *
     * @var string
     */
    protected $loader = './ancestor::body//*[contains(@data-role,"loader")]';

    /**
     * Attribute Opened
     *
     * @var string
     */
    protected $attributeOpen = './/*[@class = "title active"]/span[text()="%attributeLabel%"]';

    /**
     * Attribute tab
     *
     * @var string
     */
    protected $attributeTab = '//*[@data-role="configurable-attribute"]//*[text()="%attributeTab%"]';

    /**
     * Get attribute block
     *
     * @param string $attributeName
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Attribute
     */
    protected function getAttributeBlock($attributeName)
    {
        $attributeSelector = sprintf($this->attribute, $attributeName);
        $this->waitForElementVisible($attributeSelector, Locator::SELECTOR_XPATH);
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperAttribute(
            $this->_rootElement->find($attributeSelector, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Get product variations matrix block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Matrix
     */
    protected function getMatrixBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperConfigMatrix(
            $this->_rootElement->find($this->matrixBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Press 'Generate Variations' button
     *
     * @return void
     */
    public function generateVariations()
    {
        $browser = $this->_rootElement;
        $browser->find($this->generateVariations, Locator::SELECTOR_CSS)->click();
        $loaderSelector = $this->loader;
        $browser->waitUntil(
            function () use ($browser, $loaderSelector) {
                $loaderElement = $browser->find($loaderSelector, Locator::SELECTOR_XPATH);
                return $loaderElement->isVisible() == false ? true : null;
            }
        );
    }

    /**
     * Fill variations fieldset
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (!isset($fields['configurable_attributes_data'])) {
            return $this;
        }
        $attributes = $fields['configurable_attributes_data']['value'];
        foreach ($attributes as $attribute) {
            $this->selectAttribute($attribute['label']['value']);
        }
        $this->fillAttributeOptions($attributes);
        $this->generateVariations();
        $this->fillVariationsMatrix($fields['variations-matrix']['value']);

        return $this;
    }

    /**
     * Fill variations matrix
     *
     * @param array $fields
     * @return void
     */
    public function fillVariationsMatrix($fields)
    {
        $this->getMatrixBlock()->fillVariation($fields);
    }

    /**
     * Fill attribute options
     *
     * @param array $attributes
     * @return void
     */
    public function fillAttributeOptions(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->getAttributeBlock($attribute['label']['value'])->fillAttributeOptions($attribute);
        }
    }

    /**
     * Select attribute for variations
     *
     * @param string $attributeName
     * @return void
     */
    private function selectAttribute($attributeName)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $attributeFieldSet = $this->_rootElement
            ->find(str_replace('%attributeLabel%', $attributeName, $this->attributeOpen), Locator::SELECTOR_XPATH);
        $attributeVisible = $this->_rootElement
            ->find(str_replace('%attributeTab%', $attributeName, $this->attributeTab), Locator::SELECTOR_XPATH);
        if ($attributeVisible->isVisible()) {
            if (!$attributeFieldSet->isVisible()) {
                $attributeVisible->click();
            }
        } else {
            $this->_rootElement->find('#configurable-attribute-selector')->setValue($attributeName);
            $attributeListLocation = '#variations-search-field .mage-suggest-dropdown';
            $this->waitForElementVisible($attributeListLocation, Locator::SELECTOR_CSS);
            $attribute = $this->_rootElement->find(
                "//div[@class='mage-suggest-dropdown']//a[text()='$attributeName']",
                Locator::SELECTOR_XPATH
            );
            if ($attribute->isVisible()) {
                $attribute->click();
            }
        }
    }
}
