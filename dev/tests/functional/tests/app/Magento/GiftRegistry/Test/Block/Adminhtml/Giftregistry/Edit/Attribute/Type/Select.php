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
     * Add new option button selector
     *
     * @var string
     */
    protected $addNewOption = '[id^="registry_add_select_row_button"]';

    /**
     * Options selector
     *
     * @var string
     */
    protected $optionSelector = '(//*[@class="data-table"]//tr[@id])[%d]';

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
        $optionKey = 1;
        foreach ($options as $option) {
            $this->_rootElement->find($this->addNewOption)->click();
            /** @var Option $optionForm */
            $optionForm = $this->blockFactory->create(
                __NAMESPACE__ . '\\Option',
                [
                    'element' => $this->_rootElement->find(
                        sprintf($this->optionSelector, $optionKey),
                        Element\Locator::SELECTOR_XPATH
                    )
                ]
            );
            $optionForm->fillForm($option);
            $optionKey++;
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

    /**
     * Getting options data form on the product form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $parentFormDataOptions = parent::getDataOptions($fields, $element);
        // Data collection for options
        if (isset($fields['options'])) {
            foreach ($fields['options'] as $option) {
                $optionsBlock = $this->_rootElement->find(
                    sprintf($this->optionsBlock, $option['label']),
                    Element\Locator::SELECTOR_XPATH
                );
                /** @var AttributeForm $optionForm */
                $optionForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\\Option',
                    ['element' => $optionsBlock]
                );

                $optionData = $optionForm->getDataOptions($option, $optionsBlock);
                $parentFormDataOptions['options'][] = $optionData;
            }
        }
        return $parentFormDataOptions;
    }
}
