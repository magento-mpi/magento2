<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Attribute;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Attribute\Type\AttributeForm;

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
    protected $formSelector = '//*[@class="fieldset-wrapper opened option-box collapsable-wrapper"][last()]';

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
        $attributesFieleds = $fields['attributes']['value'];

        if (!is_array($attributesFieleds)) {
            throw new \Exception('Preset for attributes must be set.');
        }

        foreach ($attributesFieleds as $attributeFields) {
            $this->addAttribute();
            if (!isset($attributeFields['type'])) {
                throw new \Exception('Input type for attribute must be set.');
            }

            /** @var AttributeForm $attributeForm */
            $attributeForm = $this->blockFactory->create(
                __NAMESPACE__ . '\Type\\' . $this->optionNameConvert($attributeFields['type']),
                ['element' => $this->_rootElement->find($this->formSelector, Element\Locator::SELECTOR_XPATH)]
            );
            $attributeForm->fillForm($attributeFields);
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
        $this->_rootElement->find('#registry_add_new_attribute')->click();
    }

    /**
     * Prepare class name
     *
     * @param string $name
     * @return string
     */
    protected function optionNameConvert($name)
    {
        return str_replace(' ', '', ucfirst($name));
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
        $data = $this->dataMapping($fields);
        return ['attributes' => $data['attributes']['value']];
    }
}