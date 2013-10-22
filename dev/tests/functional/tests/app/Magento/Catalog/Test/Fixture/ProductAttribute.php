<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\Factory\Factory;
use Mtf\Fixture\DataFixture;

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
     * Save Attribute into Magento
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateProductAttribute($this);
        return $this;
    }

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_data = array(
            'fields' => array(
                'attribute_code' => array(
                    'value' => 'attribute_code_%isolation%',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'attribute_label' => array(
                    'value' => 'Attribute Label %isolation%',
                    'curl' => 'frontend_label[0]',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN
                ),
                'frontend_input' => array(
                    'value' => 'select',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'is_unique' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'is_global' => array(
                    'value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'is_required' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'default_value_text' => array(
                    'value' => 'Attribute Default Value %isolation%',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'frontend_class' => array(
                    'value' => '',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_MAIN,
                ),
                'frontend_label[1]' => array(
                    'value' => 'Label for Auto Generated Attribute #%isolation%',
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_LABELS,
                ),
                'is_searchable' => array(
                    'value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_visible_in_advanced_search' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_comparable' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_configurable' => array(
                    'value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_used_for_promo_rules' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_html_allowed_on_front' => array(
                    'value' => 1,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'is_visible_on_front' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'used_in_product_listing' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                ),
                'used_for_sort_by' => array(
                    'value' => 0,
                    'group' => self::GROUP_PRODUCT_ATTRIBUTE_FRONT,
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogProductAttribute($this->_dataConfig, $this->_data);
    }

    /**
     * Returns data for curl POST params
     *
     * @return array
     */
    public function getPostParams()
    {
        $fields = $this->getData('fields');
        $params = array();
        foreach ($fields as $fieldId => $fieldData) {
            $params[isset($fieldData['curl']) ? $fieldData['curl'] : $fieldId] = $fieldData['value'];
        }
        return $params;
    }
}
