<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Edit\Attribute\Type;

use Mtf\Client\Element;
use Mtf\Block\Form;

/**
 * Class AttributeForm
 * Responds for filling attribute form
 */
abstract class AttributeForm extends Form
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
    protected $optionSelector = '//tr[contains(@id,"registry_attribute") and contains(@id,"select")][last()]';

    /**
     * Filling attribute form
     *
     * @param array $attributeFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $attributeFields, Element $element = null){
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($attributeFields);
        $this->_fill($mapping, $element);
    }

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
            /** @var Option $optionForm*/
            $optionForm = $this->blockFactory->create(
                __NAMESPACE__ . '\\Option',
                ['element' => $this->_rootElement->find($this->optionSelector, Element\Locator::SELECTOR_XPATH)]
            );
            $optionForm->fillForm($option);
        }
    }
}