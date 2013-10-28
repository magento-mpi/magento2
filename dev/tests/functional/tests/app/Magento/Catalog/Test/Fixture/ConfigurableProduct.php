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
     * @var array
     */
    protected $attributes = array();

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
     * Create category
     *
     * @return string
     */
    protected function categoryProvider()
    {
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory();
        $category->switchData('subcategory');
        $category->persist();
        return $category->getCategoryName();
    }

    /**
     * @param string $code
     * @return array
     */
    protected function getConfigurableAttributeData($code)
    {
        $attribute = $this->getAttribute($code);
        return array(
            $attribute->getAttributeId() => array(
                'label'  => $attribute->getAttributeLabel(),
                'values' => $this->getOptionFixtures($attribute->getAttributeOptionIds())
            )
        );
    }

    /**
     * Returns attribute & create it if is needed
     *
     * @param string $code
     * @return \Magento\Catalog\Test\Fixture\ProductAttribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->attributes[$code])) {
            $attribute = Factory::getFixtureFactory()->getMagentoCatalogProductAttribute();
            $attribute->switchData('configurable_attribute');
            $this->attributes[$code] = $attribute->persist();
        }
        return $this->attributes[$code];
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
     * @param array $optionIds
     * @return array
     */
    protected function getOptionFixtures($optionIds)
    {
        $data = array();
        foreach ($optionIds as $num => $id) {
            $data[$id] = array(
                'pricing_value' => ($num + 1) * 1,
                'is_percent'    => 0,
                'include'       => 1
            );
        }
        return $data;
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
        foreach ($this->getData('fields/variations-matrix/value') as $variation) {
            if (is_array($variation)) {
                $variationSkus[] = $variation['value']['name']['value'];
            }
        }

        return $variationSkus;
    }

    /**
     * Create product
     *
     * @return $this|ConfigurableProduct
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateConfigurable($this);

        return $this;
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
                    'input' => 'select',
                    'input_value' => '2',
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
                'variations-matrix' => array(
                    'value' => array(
                        '0' => array(
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_label' => '%attribute_code_1%',
                                    'attribute_option' => '%attribute_1_option_label_2%'
                                )
                            ),
                            'value' => array(
                                'name' => array(
                                    'value' => 'Variation 0-%isolation%'
                                ),
                                'sku' => array(
                                    'value' => 'Variation 0-%isolation%'
                                ),
                                'qty' => array(
                                    'value' => ''
                                )
                            )
                        ),
                        '1' => array(
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_label' => '%attribute_code_1%',
                                    'attribute_option' => '%attribute_1_option_label_2%'
                                )
                            ),
                            'value' => array(
                                'name' => array(
                                    'value' => 'Variation 1-%isolation%'
                                ),
                                'sku' => array(
                                    'value' => 'Variation 1-%isolation%'
                                ),
                                'qty' => array(
                                    'value' => ''
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

    /**
     * Get configurable options
     *
     * @return array
     */
    public function getProductOptions()
    {
        $selections = $this->getData('checkout/selections');
        $options = array();
        foreach ($selections as $attributeCode => $value) {
            $attributeLabel = $this->getAttribute($attributeCode)->getAttributeLabel();
            $options[$attributeLabel] = $value;
        }
        return $options;
    }
}
