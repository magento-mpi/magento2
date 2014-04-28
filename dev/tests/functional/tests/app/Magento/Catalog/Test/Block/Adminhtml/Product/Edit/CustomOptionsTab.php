<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Edit;

use Mtf\ObjectManager;
use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Custom Options Tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class CustomOptionsTab extends Tab
{
    /**
     * Subform of the main tab form
     *
     * @var array
     */
    protected $childrenForm = [
        'Field' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab\OptionField',
        'Drop-down' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab\OptionDropDown'
    ];

    /**
     * Add an option button
     *
     * @var string
     */
    protected $buttonFormLocator = '[data-ui-id="admin-product-options-add-button"]';

    /**
     * Fill custom options
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $fields = reset($fields);
        if (empty($fields['value'])) {
            return $this;
        }

        $isolationMapping = $this->mapping;
        foreach ($fields['value'] as $row => $field) {
            $row += 1;
            $this->mapping = $this->preparingSelectors(
                ['%row%' => $row]
            );

            $this->_rootElement->find($this->buttonFormLocator)->click();
            $data = $this->dataMapping($field);
            $this->_fill($data, $element);

            // Fill subform
            if (isset($field['type']) && isset($this->childrenForm[$field['type']])
                && !empty($field['options'])
            ) {
                /**@var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm*/
                $optionsForm = ObjectManager::getInstance()->create(
                    $this->childrenForm[$field['type']],
                    ['element' => $element]
                );

                $optionIsolationMapping = $optionsForm->getMapping();
                foreach ($field['options'] as $optionRow => $option) {
                    $placeholder = [
                        '%row-1%' => $row,
                        '%row-2%' => $optionRow + 1
                    ];
                    $mapping = $this->preparingSelectors(
                        $placeholder,
                        $optionIsolationMapping
                    );

                    $optionsForm->setMapping($mapping);
                    $optionsForm->fillAnArray($option, $placeholder);
                }
            }
            $this->mapping = $isolationMapping;
        }

        return $this;
    }
}