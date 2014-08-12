<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute\Type\AttributeForm;

/**
 * Class Attribute
 * Attribute handler class
 */
class Attribute extends Tab
{
    /**
     * Form selector
     *
     * @var string
     */
    protected $formSelector = '(//div[contains(@id,"registry_option_") and contains(@class,"fieldset-")])[last()]';

    /**
     * Selector for attribute block by code
     *
     * @var string
     */
    protected $attributeBlock = './/*[contains(@class,"option") and (.//*[contains(@name,"[label]") and @value="%s"])]';

    /**
     * Selector for options block
     *
     * @var string
     */
    protected $optionsBlock = './/tr[td/input[@value="%s"] and contains(@id,"registry_attribute")]';

    /**
     * Add attribute button selector
     *
     * @var string
     */
    protected $addAttribute = '#registry_add_new_attribute';

    /**
     * Fill data to fields on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @throws \Exception
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $attributesFields = $fields['attributes']['value'];

        foreach ($attributesFields as $attributeField) {
            $this->addAttribute();
            if (!isset($attributeField['type'])) {
                throw new \Exception('Input type for attribute must be set.');
            }

            /** @var AttributeForm $attributeForm */
            $attributeForm = $this->blockFactory->create(
                __NAMESPACE__ . '\Type\\' . $this->optionNameConvert($attributeField['type']),
                ['element' => $this->_rootElement->find($this->formSelector, Element\Locator::SELECTOR_XPATH)]
            );
            $attributeForm->fillForm($attributeField);
        }
        return $this;
    }

    /**
     * Click Add Attribute button
     *
     * @return void
     */
    protected function addAttribute()
    {
        $this->_rootElement->find($this->addAttribute)->click();
    }

    /**
     * Prepare class name
     *
     * @param string $name
     * @return string
     */
    protected function optionNameConvert($name)
    {
        $name = explode('/', $name);
        return str_replace(' ', '', $name[1]);
    }

    /**
     * Get data of tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        $fields = reset($fields);
        $formData = [];
        if (empty($fields['value'])) {
            return $formData;
        }

        foreach ($fields['value'] as $keyRoot => $field) {
            $rootElement = $this->_rootElement->find(
                sprintf($this->attributeBlock, $field['label']),
                Element\Locator::SELECTOR_XPATH
            );
            /** @var AttributeForm $attributeForm */
            $attributeForm = $this->blockFactory->create(
                __NAMESPACE__ . '\Type\\' . $this->optionNameConvert($field['type']),
                ['element' => $rootElement]
            );

            $formData['attributes'][$keyRoot] = $attributeForm->getDataOptions($field, $rootElement);

            // Data collection for options
            if (isset($field['options'])) {
                foreach ($field['options'] as $option) {
                    $optionsBlock = $this->_rootElement->find(
                        sprintf($this->optionsBlock, $option['label']),
                        Element\Locator::SELECTOR_XPATH
                    );
                    /** @var AttributeForm $optionForm */
                    $optionForm = $this->blockFactory->create(
                        __NAMESPACE__ . '\Type\Option',
                        ['element' => $optionsBlock]
                    );

                    $optionData = $optionForm->getDataOptions($option, $optionsBlock);
                    $formData['attributes'][$keyRoot]['options'][] = $optionData;
                }
            }
        }
        return $formData;
    }
}