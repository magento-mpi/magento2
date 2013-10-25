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

use Mtf\System\Config;
use Mtf\Factory\Factory;

/**
 * Class ConfigurableProduct
 * Configurable product data
 *
 * @package Magento\Catalog\Test\Fixture
 */
class ConfigurableProduct extends Product
{
    /**
     * Mapping data into ui tabs
     */
    const GROUP_VARIATIONS = 'product_info_tabs_super_config_content';

    /**
     * Custom constructor to create configurable product with attribute
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['category'] = array($this, 'categoryProvider');
        $this->_placeholders['attribute_label_1'] = array($this, 'attributeProvider');
    }

    /**
     * Persists prepared data into application
     *
     * @return $this|ConfigurableProduct
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateConfigurable($this);

        return $this;
    }

    /**
     * Create new configurable attribute and add it to product
     *
     * @return string
     */
    protected function attributeProvider()
    {
        $attribute = Factory::getFixtureFactory()->getMagentoCatalogProductAttribute();
        $attribute->switchData('configurable_attribute');
        $attribute->persist();
        $this->_dataConfig['attributes']['id'][] = $attribute->getAttributeId();
        $this->_dataConfig['attributes'][$attribute->getAttributeId()]['code'] = $attribute->getAttributeCode();
        $this->_dataConfig['options'][$attribute->getAttributeId()]['id'] = $attribute->getOptionIds();

        $options = $attribute->getOptionLabels();
        $placeholders['attribute_code_1'] = $attribute->getAttributeCode();
        $placeholders['attribute_1_option_label_1'] = $options[0];
        $placeholders['attribute_1_option_label_2'] = $options[1];
        $this->_applyPlaceholders($this->_data, $placeholders);

        return $attribute->getAttributeLabel();
    }

    /**
     * Create product
     * Get affected attribute set
     *
     * @return string|null
     */
    public function getAffectedAttributeSet()
    {
        return $this->getData('affect_configurable_product_attributes')
            ? $this->getData('affect_configurable_product_attributes')
            : null;
    }

    /**
     * Get variations SKUs
     *
     * @return $this|ConfigurableProduct
     */
    public function getVariationSkus()
    {
        $variationSkus = array();
        foreach ($this->getData('fields/variation-matrix/value') as $variation) {
            if (is_array($variation)) {
                $variationSkus[] = $variation['name']['value'];
            }
        }

        return $variationSkus;
    }

    /**
     * Get configurable product options
     *
     * @return array
     */
    public function getConfigurableOptions()
    {
        $options = array();
        foreach($this->getData('fields/configurable_attributes_data/value') as $attribute) {
            foreach($attribute as $option) {
                if (isset($option['option_label']['value'])) {
                    $options[$attribute['label']['value']][] = $option['option_label']['value'];
                }
            }
        }

        return $options;
    }

    /**
     * Init Data
     */
    protected function _initData()
    {
        $this->_dataConfig = array(
            'constraint' => 'Success',

            'create_url_params' => array(
                'type' => 'configurable',
                'set' => 4,
            ),
        );
        $this->_data = array(
            'fields' => array(
                'name' => array(
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'sku' => array(
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'price' => array(
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'tax_class_id' => array(
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'weight' => array(
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'configurable_attributes_data' => array(
                    'value' => array(
                            '0' => array(
                                'label' => array(
                                    'value' => '%attribute_label_1%'
                                ),
                                '0' => array(
                                    'option_label' => array(
                                        'value' => '%attribute_1_option_label_1%'
                                    ),
                                    'pricing_value' => array(
                                        'value' => '10'
                                    ),
                                    'is_percent' => array(
                                        'value' => 'Yes'
                                    ),
                                    'include' => array(
                                        'value' => 'No'
                                    ),
                                ),
                                '1' => array(
                                    'option_label' => array(
                                        'value' => '%attribute_1_option_label_2%'
                                    ),
                                    'pricing_value' => array(
                                        'value' => '20'
                                    ),
                                    'is_percent' => array(
                                        'value' => 'No'
                                    ),
                                    'include' => array(
                                        'value' => 'Yes'
                                    ),
                                )
                            )
                        ),
                    'group' => static::GROUP_VARIATIONS
                ),
                'variation-matrix' => array(
                    'value' => array(
                        '0' => array(
                            'name' => array(
                                'value' => 'Variation 0-%isolation%'
                            ),
                            'sku' => array(
                                'value' => 'Variation 0-%isolation%'
                            ),
                            'quantity_and_stock_status' => array(
                                'qty' => array(
                                    'value' => ''
                                )
                            ),
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_label' => '%attribute_code_1%',
                                    'attribute_option' => '%attribute_1_option_label_2%'
                                )
                            )
                        ),
                        '1' => array(
                            'name' => array(
                                'value' => 'Variation 1-%isolation%'
                            ),
                            'sku' => array(
                                'value' => 'Variation 1-%isolation%'
                            ),
                            'quantity_and_stock_status' => array(
                                'qty' => array(
                                    'value' => ''
                                )
                            ),
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_label' => '%attribute_code_1%',
                                    'attribute_option' => '%attribute_1_option_label_2%'
                                )
                            )
                        )
                    ),
                    'group' => static::GROUP_VARIATIONS
                )
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogConfigurableProduct($this->_dataConfig, $this->_data);
    }
}
