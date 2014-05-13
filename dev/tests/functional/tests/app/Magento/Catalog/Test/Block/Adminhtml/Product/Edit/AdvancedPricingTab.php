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
 * Class AdvancedPricingTab
 * Product advanced pricing tab
 */
class AdvancedPricingTab extends Tab
{
    /**
     * Class name 'Subform' of the main tab form
     *
     * @var array
     */
    protected $childrenForm = [
        'group_price' => 'AdvancedPricingTab\OptionGroup',
        'tier_price' => 'AdvancedPricingTab\OptionTier'
    ];

    /**
     * Fill 'Advanced price' product form on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields as $fieldName => $field) {
            // Fill form
            if (isset($this->childrenForm[$fieldName]) && is_array($field['value'])) {
                /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm */
                $optionsForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\\' . $this->childrenForm[$fieldName],
                    ['element' => $this->_rootElement]
                );

                foreach ($field['value'] as $key => $option) {
                    ++$key;
                    $optionsForm->fillOptions(
                        $option,
                        $this->_rootElement->find('#attribute-' .
                            $fieldName . '-container tbody tr:nth-child(' . $key . ')'
                        )
                    );
                }
            } elseif (!empty($field['value'])) {
                $data = $this->dataMapping([$fieldName => $field]);
                $this->_fill($data, $this->_rootElement);
            }
        }

        return $this;
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
        $formData = [];
        foreach ($fields as $fieldName => $field) {
            // Data collection forms
            if (isset($this->childrenForm[$fieldName]) && is_array($field['value'])) {
                /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm */
                $optionsForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\\' . $this->childrenForm[$fieldName],
                    ['element' => $this->_rootElement]
                );

                foreach ($field['value'] as $key => $option) {
                    $formData[$fieldName][$key++] = $optionsForm->getDataOptions(
                        $option,
                        $this->_rootElement->find('#attribute-' .
                            $fieldName . '-container tbody tr:nth-child(' . $key . ')'
                        )
                    );
                }
            } elseif (!empty($field['value'])) {
                $data = $this->dataMapping([$fieldName => $field]);
                $formData[$fieldName] = $this->_getData($data, $this->_rootElement);
            }
        }

        return $formData;
    }
}
