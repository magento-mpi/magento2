<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;
use Mtf\Client\Element\Locator;

/**
 * Class Attribute
 */
class ProductAttribute extends DataFixture
{
    /**
     * Logical sets for mapping data into tabs
     */
    const GROUP_PRODUCT_ATTRIBUTE_MAIN = 'product_attribute_tabs_main';
    const GROUP_PRODUCT_ATTRIBUTE_LABELS = 'product_attribute_tabs_labels';
    const GROUP_PRODUCT_ATTRIBUTE_FRONT = 'product_attribute_tabs_front';

    /**
     * Get attribute name
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return $this->getData('fields/frontend_label/value');
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return $this->getData('fields/attribute_code/value');
    }

    /**
     * Get attribute option ids
     *
     * @return array
     */
    public function getOptionIds()
    {
        return $this->getData('fields/option_ids');
    }

    /**
     * Get attribute options labels
     *
     * @return array
     */
    public function getOptionLabels()
    {
        $options = $this->getOptions();
        $optionsLabels = array();
        foreach ($options as $option) {
            $optionsLabels[] = $option['label']['value'];
        }
        return $optionsLabels;
    }

    /**
     * Get attribute options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getData('options/value');
        return is_array($options) ? $options : array();
    }

    /**
     * Get attribute id
     *
     * @return string
     */
    public function getAttributeId()
    {
        return $this->getData('fields/attribute_id/value');
    }

    /**
     * Save Attribute into Magento
     */
    public function persist()
    {
        $attributeIds = Factory::getApp()->magentoCatalogCreateProductAttribute($this);
        $this->_data['fields']['attribute_id']['value'] = $attributeIds['attributeId'];
        $this->_data['fields']['option_ids'] = $attributeIds['optionIds'];

        return $this;
    }

    /**
     * {inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'attribute_code' => array(
                    'value' => 'attribute_code_%isolation%',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'frontend_label' => array(
                    'value' => 'Attribute %isolation%',
                    'input_name' => 'frontend_label[0]',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN
                ),
                'frontend_input' => array(
                    'value' => 'Dropdown',
                    'input' => 'select',
                    'input_value' => 'select',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'is_configurable' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'is_searchable' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_visible_in_advanced_search' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_comparable' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_filterable' => array(
                    'value' => 'Filterable (with results)',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_visible_on_front' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_filterable_in_search' => array(
                    'value' => 'Yes',
                    'input' => 'select',
                    'input_value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
            ),
            'options' => array(
                'value' => array(
                    'option_1' => array(
                        'label' => array(
                            'value' => 'Option 1 %isolation%',
                            'input_name' => 'option[value][option_0][0]',
                            'selector' => '//*[@id="manage-options-panel"]/table/tbody/tr[1]/td[3]/input',
                            'strategy' => Locator::SELECTOR_XPATH,
                        ),
                        'default' => array(
                            'value' => 'Yes',
                            'input' => 'checkbox',
                            'input_name' => 'default[0]',
                            'input_value' => 'option_0',
                            'selector' => '//*[@id="manage-options-panel"]/table/tbody/tr[1]/td[2]/input',
                            'strategy' => Locator::SELECTOR_XPATH,
                        ),
                    ),
                    'option_2' => array(
                        'label' => array(
                            'value' => 'Option 2 %isolation%',
                            'input_name' => 'option[value][option_1][0]',
                            'selector' => '//*[@id="manage-options-panel"]/table/tbody/tr[2]/td[3]/input',
                            'strategy' => Locator::SELECTOR_XPATH,
                        ),
                        'default' => array(
                            'value' => 'No',
                            'input' => 'checkbox',
                            'input_name' => 'default[1]',
                            'input_value' => 'option_1',
                            'selector' => '//*[@id="manage-options-panel"]/table/tbody/tr[2]/td[2]/input',
                            'strategy' => Locator::SELECTOR_XPATH,
                        ),
                    ),
                ),
            ),
        );
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogProductAttribute($this->_dataConfig, $this->_data);
    }
}
