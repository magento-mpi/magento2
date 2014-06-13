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
 * Class CustomOptionsTab
 * Product custom options tab
 */
class CustomOptionsTab extends Tab
{
    /**
     * Custom option row CSS locator
     *
     * @var string
     */
    protected $customOptionRow = '#product-custom-options-content .fieldset-wrapper:nth-child(%d)';

    /**
     * Add an option button
     *
     * @var string
     */
    protected $buttonFormLocator = '[data-ui-id="admin-product-options-add-button"]';

    /**
     * Fill custom options form on tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $fields = reset($fields );
        if (empty($fields['value']) || !is_array($fields['value'])) {
            return $this;
        }

        foreach ($fields['value'] as $keyRoot => $field) {
            $options = null;
            $this->_rootElement->find($this->buttonFormLocator)->click();
            if (!empty($field['options'])) {
                $options = $field['options'];
                unset($field['options']);
            }

            $rootElement = $this->_rootElement->find(sprintf($this->customOptionRow, $keyRoot + 1));
            $data = $this->dataMapping($field);
            $this->_fill($data, $rootElement);

            // Fill subform
            if (isset($field['type']) && !empty($options)) {
                /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm */
                $optionsForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\CustomOptionsTab\Option' . $this->optionConvert($field['type']),
                    ['element' => $rootElement]
                );

                foreach ($options as $key => $option) {
                    ++$key;
                    $optionsForm->fillOptions(
                        $option,
                        $rootElement->find('.fieldset .data-table tbody tr:nth-child(' . $key . ')')
                    );
                }
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
        $fields = reset($fields);
        $formData = [];
        if (empty($fields['value'])) {
            return $formData;
        }

        foreach ($fields['value'] as $keyRoot => $field) {
            $formDataItem = null;
            $options = null;
            if (!empty($field['options'])) {
                $options = $field['options'];
                unset($field['options']);
            }

            $rootLocator = sprintf($this->customOptionRow, $keyRoot + 1);
            $rootElement = $this->_rootElement->find($rootLocator);
            $this->waitForElementVisible($rootLocator);
            $data = $this->dataMapping($field);
            $formDataItem = $this->_getData($data, $rootElement);

            // Data collection subform
            if (isset($field['type']) && !empty($options)) {
                /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm */
                $optionsForm = $this->blockFactory->create(
                    __NAMESPACE__ . '\CustomOptionsTab\Option' . $this->optionConvert($field['type']),
                    ['element' => $rootElement]
                );

                foreach ($options as $key => $option) {
                    $formDataItem['options'][$key++] = $optionsForm->getDataOptions(
                        $option,
                        $rootElement->find('.fieldset .data-table tbody tr:nth-child(' . $key . ')')
                    );
                }
            }
            $formData[$fields['attribute_code']][$keyRoot] = $formDataItem;
        }

        return $formData;
    }

    /**
     * Convert string
     *
     * @param string $str
     * @return string
     */
    protected function optionConvert($str)
    {
        $str = str_replace([' ', '&'], "", $str);
        if ($end = strpos($str, '-')) {
            $str = substr($str, 0, $end) . ucfirst(substr($str, ($end + 1)));
        }
        return $str;
    }
}
