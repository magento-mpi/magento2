<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Fixture;

use Magento\Catalog\Test\Fixture\ProductAttribute;
use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product;
use Magento\ConfigurableProduct\Test\Repository\ConfigurableProduct as Repository;

/**
 * Class ConfigurableProduct
 * Configurable product data
 */
class ConfigurableProduct extends Product
{
    /**
     * Mapping data into ui tabs
     */
    const GROUP = 'variations';

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Custom constructor to create configurable product with attribute
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = [])
    {
        parent::__construct($configuration, $placeholders);

        $this->_placeholders['attribute_label_1'] = [$this, 'attributeProvider'];
    }

    /**
     * Provide data to product from new attribute
     *
     * @param ProductAttribute $attribute
     * @return void
     */
    public function provideNewAttributeData(ProductAttribute $attribute)
    {
        $options = $attribute->getOptionLabels();
        $placeholders['new_attribute_label'] = $attribute->getFrontendLabel();
        $placeholders['new_attribute_option_1_label'] = $options[0];
        $placeholders['new_attribute_option_2_label'] = $options[1];
        $this->_applyPlaceholders($this->_data, $placeholders);
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
        $placeholders['attribute_1_name'] = $attribute->getFrontendLabel();
        $placeholders['attribute_1_option_label_1'] = $options[0];
        $placeholders['attribute_1_option_label_2'] = $options[1];
        $this->_applyPlaceholders($this->_data, $placeholders);

        return $attribute->getFrontendLabel();
    }

    /**
     * Create product
     * Get affected attribute set
     *
     * @return string|null
     */
    public function getAffectedAttributeSet()
    {
        return $this->getData('affected_attribute_set')
            ? $this->getData('affected_attribute_set')
            : null;
    }

    /**
     * Returns the sku for the specified option.
     *
     * @param string $selectedOption
     * @return string
     */
    public function getVariationSku($selectedOption)
    {
        $sku = '';
        $configurableAttributes = $this->getConfigurableAttributes();
        $attributeKey = $selectedOption['title'];
        $optionKey = $selectedOption['value'];
        $optionValue = $configurableAttributes[$attributeKey][$optionKey]['option_label']['value'];

        foreach ($this->getVariationsMatrix() as $variation) {
            foreach ($configurableAttributes as $configurableAttribute) {
                foreach ($configurableAttribute as $option) {
                    if (isset($option['option_label']['value'])
                        && $option['option_label']['value'] == $optionValue) {
                        $sku = $variation['value']['sku']['value'];
                        break 3;
                    }
                }
            }
        }
        return $sku;
    }

    /**
     * Get variations SKUs
     *
     * @return $this|ConfigurableProduct
     */
    public function getVariationSkus()
    {
        $variationSkus = [];
        foreach ($this->getVariationsMatrix() as $variation) {
            if (is_array($variation) && isset($variation['value']['name']['value'])) {
                $variationSkus[] = $variation['value']['name']['value'];
            }
        }

        return $variationSkus;
    }

    /**
     * Get variations matrix
     *
     * @return array
     */
    public function getVariationsMatrix()
    {
        $variations = $this->getData('fields/variations-matrix/value');
        return is_array($variations) ? $variations : [];
    }

    /**
     * Create product
     *
     * @return $this|ConfigurableProduct
     */
    public function persist()
    {
        $id = Factory::getApp()->magentoConfigurableProductCreateConfigurable($this);
        $this->_data['id']['value'] = $id;

        return $this;
    }

    /**
     * Get configurable attributes data
     *
     * @return array
     */
    public function getConfigurableAttributes()
    {
        $attributes = $this->getData('fields/configurable_attributes_data/value');
        return is_array($attributes) ? $attributes : [];
    }

    /**
     * Get configurable product options
     *
     * @return array
     */
    public function getConfigurableOptions()
    {
        $options = [];
        foreach ($this->getConfigurableAttributes() as $attribute) {
            foreach ($attribute as $option) {
                if (isset($option['option_label']['value'])) {
                    $options[$attribute['label']['value']][] = $option['option_label']['value'];
                }
            }
        }
        return $options;
    }

    /**
     * Init Data
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'constraint' => 'Success',

            'create_url_params' => array(
                'type' => Repository::CONFIGURABLE,
                'set' => static::DEFAULT_ATTRIBUTE_SET_ID,
            ),
        );
        $data = array(
            'fields' => array(
                'price' => array(
                    'value' => '10',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'tax_class_id' => array(
                    'value' => 'Taxable Goods',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select',
                    'input_value' => '2',
                ),
                'weight' => array(
                    'value' => '1',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'product_website_1' => array(
                    'value' => 'Yes',
                    'input_value' => array(1),
                    'group' => static::GROUP_PRODUCT_WEBSITE,
                    'input' => 'checkbox',
                    'input_name' => 'website_ids'
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
                                    'value' => '1'
                                ),
                                'is_percent' => array(
                                    'value' => 'No'
                                ),
                                'include' => array(
                                    'value' => 'Yes'
                                ),
                            ),
                            '1' => array(
                                'option_label' => array(
                                    'value' => '%attribute_1_option_label_2%'
                                ),
                                'pricing_value' => array(
                                    'value' => '2'
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
                    'group' => static::GROUP
                ),
                'variations-matrix' => array(
                    'value' => array(
                        '0' => array(
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_option' => '%attribute_1_option_label_1%'
                                )
                            ),
                            'value' => array(
                                'display' => array(
                                    'value' => 'Yes',
                                    'input' => 'checkbox'
                                ),
                                'name' => array(
                                    'value' => 'Variation 0-%isolation%'
                                ),
                                'sku' => array(
                                    'value' => 'Variation 0-%isolation%'
                                ),
                                'qty' => array(
                                    'value' => '100'
                                )
                            )
                        ),
                        '1' => array(
                            'configurable_attribute' => array(
                                '0' => array(
                                    'attribute_option' => '%attribute_1_option_label_2%'
                                )
                            ),
                            'value' => array(
                                'display' => array(
                                    'value' => 'Yes',
                                    'input' => 'checkbox'
                                ),
                                'name' => array(
                                    'value' => 'Variation 1-%isolation%'
                                ),
                                'sku' => array(
                                    'value' => 'Variation 1-%isolation%'
                                ),
                                'qty' => array(
                                    'value' => '200'
                                )
                            )
                        )
                    ),
                    'group' => static::GROUP
                ),
            ),
            'checkout' => array(
                'selections' => array(
                    '0' => array(
                        'title' => '0',
                        'value' => '0'
                    )
                ),
                'special_price' => '10'
            )
        );

        $this->_data = array_merge_recursive($this->_data, $data);

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoConfigurableProductConfigurableProduct($this->_dataConfig, $this->_data);
    }

    /**
     * Get configurable options
     *
     * @return array
     */
    public function getProductOptions()
    {
        return $this->getData('checkout/selections');
    }

    /**
     * Return special price for first configurable option
     * This value is used to validate value on the cart and order
     *
     * @return string
     */
    public function getProductSpecialPrice()
    {
        return $this->getData('checkout/special_price');
    }

    /**
     * Get product options price
     *
     * @return float|int
     */
    public function getProductOptionsPrice()
    {
        $price = 0;
        $selections = $this->getData('checkout/selections');
        $attributes = $this->getData('fields/configurable_attributes_data/value');

        foreach ($selections as $selection) {
            $price += $attributes[$selection['title']][$selection['value']]['pricing_value']['value'];
        }
        return $price;
    }

    /**
     * Prepare edit configurable product data
     *
     * @return $this
     */
    public function getEditData()
    {
        $data = $this->getData();
        $this->switchData('edit_configurable');
        $editData = $this->getData();
        $data['fields']['variations-matrix'] = $editData['fields']['variations-matrix'];
        $this->_data = array_replace_recursive($data, $editData);
        return $this;
    }
}
