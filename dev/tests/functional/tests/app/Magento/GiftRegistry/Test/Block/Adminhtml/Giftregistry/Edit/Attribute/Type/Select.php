<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Giftregistry\Edit\Attribute\Type;

use Mtf\Client\Element;

/**
 * Class Select
 * Filling select type attribute
 */
class Select extends AttributeForm
{
    /**
     * Selector for options block
     *
     * @var string
     */
    protected $optionsBlock = './/tr[td/input[@value="%s"] and contains(@id,"registry_attribute")]';

    /**
     * Fill attribute options
     *
     * @param array $options
     * @return void
     */
    protected function fillOptions(array $options)
    {
        foreach ($options as $option) {
            $this->_rootElement->find($this->addNewOption)->click();
            /** @var Option $optionForm */
            $optionForm = $this->blockFactory->create(
                __NAMESPACE__ . '\\Option',
                ['element' => $this->_rootElement->find($this->optionSelector, Element\Locator::SELECTOR_XPATH)]
            );
            $optionForm->fillForm($option);
        }
    }

    /**
     * Filling attribute form
     *
     * @param array $attributeFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $attributeFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($attributeFields);
        $this->_fill($mapping, $element);
        $this->fillOptions($mapping['options']['value']);
    }
}
