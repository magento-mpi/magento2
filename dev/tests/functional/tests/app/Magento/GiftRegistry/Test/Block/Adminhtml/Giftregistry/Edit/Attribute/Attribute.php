<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute;

use Magento\Backend\Test\Block\Widget\Tab;
use Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute\Type\AttributeForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

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
    protected $formSelector = '//*[@id="registry_attribute_container"]/following::fieldset[%d]';

    /**
     * Selector for attribute block by label
     *
     * @var string
     */
    protected $attributeBlock = './/fieldset[.//input[contains(@name, "label") and @value = "%s"]]';

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
        $attributeKey = 1;
        foreach ($fields['attributes']['value'] as $attributeField) {
            $this->addAttribute();
            if (!isset($attributeField['type'])) {
                throw new \Exception('Input type for attribute must be set.');
            }

            /** @var AttributeForm $attributeForm */
            $attributeForm = $this->blockFactory->create(
                __NAMESPACE__ . '\Type\\' . $this->optionNameConvert($attributeField['type']),
                [
                    'element' => $this->_rootElement->find(
                        sprintf($this->formSelector, $attributeKey),
                        Locator::SELECTOR_XPATH
                    )
                ]
            );
            $attributeForm->fillForm($attributeField);
            $attributeKey++;
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
        }
        return $formData;
    }
}
