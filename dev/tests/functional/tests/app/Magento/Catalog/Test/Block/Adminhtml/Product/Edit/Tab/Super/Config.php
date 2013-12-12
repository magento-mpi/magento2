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

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Variations
 * Adminhtml catalog super product configurable tab
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super
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
    protected $generateVariations = '[data-ui-id=product-variations-generator-generate]';

    /**
     * Product variations matrix block
     *
     * @var string
     */
    protected $matrixBlock = '[data-role=product-variations-matrix] table';

    /**
     * Open Variations section
     *
     * @param Element $context
     */
    public function open(Element $context = null)
    {
        $element = $context ? : $this->_rootElement;
        $element->find(static::GROUP_PRODUCT_DETAILS, Locator::SELECTOR_ID)->click();
    }

    /**
     * Get attribute block
     *
     * @param $attributeName
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Attribute
     */
    protected function getAttributeBlock($attributeName)
    {
        $attributeBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperAttribute(
            $this->_rootElement->find('//div[*/*/span="' . $attributeName . '"]', Locator::SELECTOR_XPATH)
        );

        return $attributeBlock;
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
        $this->getMatrixBlock()->fillVariation($fields['variations-matrix']['value']);
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
