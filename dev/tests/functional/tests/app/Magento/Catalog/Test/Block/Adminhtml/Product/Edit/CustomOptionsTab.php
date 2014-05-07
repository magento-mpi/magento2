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

/**
 * Class CustomOptionsTab
 * Product custom options tab
 *
 * @package Magento\Catalog\Test\Block\Adminhtml\Product\Edit
 */
class CustomOptionsTab extends Options
{
    /**
     * Class name 'Subform' of the main tab form
     *
     * @var array
     */
    protected $childrenForm = [
        'Field' => 'CustomOptionsTab\OptionField',
        'Drop-down' => 'CustomOptionsTab\OptionDropDown'
    ];

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
     * @param Element $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element)
    {
        $fields = reset($fields );
        if (empty($fields['value'])) {
            return $this;
        }

        foreach ($fields['value'] as $keyRoot => $field) {
            ++$keyRoot;
            $options = null;
            $this->_rootElement->find($this->buttonFormLocator)->click();
            if (!empty($field['options'])) {
                $options = $field['options'];
                unset($field['options']);
            }

            $rootElement = $this->_rootElement->find('#option_' . $keyRoot);
            $data = $this->dataMapping($field);
            $this->_fill($data, $rootElement);

            // Fill subform
            if (isset($field['type']) && isset($this->childrenForm[$field['type']])
                && !empty($options)
            ) {
                /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Options $optionsForm */
                $optionsForm = ObjectManager::getInstance()->create(
                    __NAMESPACE__ . '\\' . $this->childrenForm[$field['type']],
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
}
