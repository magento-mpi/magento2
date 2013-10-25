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

namespace Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Variations
 * Adminhtml catalog super product configurable tab
 *
 * @package Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super
 */
class Config extends Tab
{
    /**
     * Tab where bundle options section is placed
     */
    const GROUP_PRODUCT_DETAILS = 'product_info_tabs_product-details';

    /**
     * 'Generate Variations' button
     *
     * @var string
     */
    private $generateVariations;

    /**
     * Attribute block in Variation section
     *
     * @var Attribute
     */
    private $attributeBlock;

    /**
     * Product variations matrix block
     *
     * @var Config\Matrix
     */
    private $matrixBlock;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        //Elements
        $this->generateVariations = '[data-ui-id=product-variations-generator-generate]';
        //Blocks
        $this->matrixBlock = Factory::getBlockFactory()->getMagentoBackendCatalogProductEditTabSuperConfigMatrix(
            $this->_rootElement->find('[data-role=product-variations-matrix] table', Locator::SELECTOR_CSS)
        );
    }

    /**
     * Open Variations section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? $context : $this->_rootElement;
        $element->find(static::GROUP_PRODUCT_DETAILS, Locator::SELECTOR_ID)->click();
    }

    /**
     * Get attribute block
     *
     * @param $attributeName
     *
     * @return Attribute
     */
    private function getAttributeBlock($attributeName)
    {
        $this->attributeBlock = Factory::getBlockFactory()->getMagentoBackendCatalogProductEditTabSuperAttribute(
            $this->_rootElement->find('//div[*/*/span="' . $attributeName . '"]', Locator::SELECTOR_XPATH)
        );

        return $this->attributeBlock;
    }

    /**
     * Get product variations matrix block
     *
     * @return \Magento\Backend\Test\Block\Catalog\Product\Edit\Tab\Super\Config\Matrix
     */
    private function getMatrixBlock()
    {
        return $this->matrixBlock;
    }

    /**
     * Press 'Generate Variations' button
     */
    public function generateVariations()
    {
        $this->_rootElement->find($this->generateVariations, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Fill variations fieldset
     *
     * @param array $fields
     * @param Element $element
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $attributes = array();
        foreach ($fields['configurable_attributes_data']['value'] as $attribute) {
            $this->selectAttribute($attribute['label']['value']);
            $attributes[$attribute['label']['value']] = $attribute;
        }
        foreach ($attributes as $key => $attribute) {
            $this->getAttributeBlock($key)->fillAttributeOptions($attribute);
        }
        $this->generateVariations();
        $this->getMatrixBlock()->fillVariation($fields['variation-matrix']['value']);
    }

    /**
     * Select attribute for variations
     *
     * @param string $attributeName
     */
    private function selectAttribute($attributeName)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $this->_rootElement->find('#configurable-attribute-selector')->setValue($attributeName);
        $attributeListLocation = '#variations-search-field .mage-suggest-dropdown';
        $this->waitForElementVisible($attributeListLocation, Locator::SELECTOR_CSS);
        $attribute = $this->_rootElement->find("//div[@class='mage-suggest-dropdown']//a[text()='$attributeName']",
            Locator::SELECTOR_XPATH);
        if ($attribute->isVisible()) {
            $attribute->click();
        }
    }
}
