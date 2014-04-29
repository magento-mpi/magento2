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
 * Custom options tab
 *
 * @package Magento\Catalog\Test\Block\Product
 */
class AdvancedPricingTab extends Tab
{
    /**
     * Subform of the main tab form
     *
     * @var array
     */
    protected $childrenForm = [
        'group_price' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab\OptionGroup',
        'tier_price' => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\AdvancedPricingTab\OptionTier'
    ];

    /**
     * Fill group price options
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        foreach ($fields as $fieldName => $field) {

            // Fill form
            if (isset($this->childrenForm[$fieldName]) && is_array($field['value'])) {

                /**@var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm*/
                $optionsForm = ObjectManager::getInstance()->create(
                    $this->childrenForm[$fieldName],
                    ['element' => $element]
                );

                $optionIsolationMapping = $optionsForm->getMapping();
                foreach ($field['value'] as $row => $option) {

                    $placeholder = ['%row%' => $row];
                    $mapping = $this->preparingSelectors(
                        $placeholder,
                        $optionIsolationMapping
                    );

                    $optionsForm->setMapping($mapping);
                    $optionsForm->fillAnArray($option, $placeholder);
                }

            } elseif (!empty($field['value'])) {

                $data = $this->dataMapping([$fieldName => $field]);
                $this->_fill($data, $element);
            }
        }

        return $this;
    }

    /**
     * Verify data to fields on tab
     *
     * @param array $fields
     * @param Element $element
     *
     * @return bool
     */
    public function verifyFormTab(array $fields, Element $element)
    {
        foreach ($fields as $fieldName => $field) {

            // Verify form
            if (isset($this->childrenForm[$fieldName]) && is_array($field['value'])) {

                /**@var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm*/
                $optionsForm = ObjectManager::getInstance()->create(
                    $this->childrenForm[$fieldName],
                    ['element' => $element]
                );

                $optionIsolationMapping = $optionsForm->getMapping();
                foreach ($field['value'] as $row => $option) {

                    $placeholder = ['%row%' => $row];
                    $mapping = $this->preparingSelectors(
                        $placeholder,
                        $optionIsolationMapping
                    );

                    $optionsForm->setMapping($mapping);
                    $optionsForm->verifyAnArray($option, $placeholder);
                }

            } elseif (!empty($field['value'])) {

                $data = $this->dataMapping([$fieldName => $field]);
                $this->_verify($data, $element);
            }
        }

        return $this;
    }
}