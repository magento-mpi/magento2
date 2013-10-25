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
use \Magento\Catalog\Test\Fixture\Product;

/**
 * Class ConfigurableProduct
 * Configurable product data
 *
 * @package Magento\Catalog\Test\Fixture
 */
class ConfigurableProduct extends Product
{
    protected $attributes = array();

    /**
     * @param string $code
     * @return array
     */
    protected function getOptionVariations($code)
    {
        $attribute = $this->getAttribute($code);
        $optionIds = $attribute->getAttributeOptionIds();
        $data = array();
        foreach ($optionIds as $num => $id) {
            $data[$id] = array(
                'name'      => 'variation %isolation%' . $num,
                'sku'       => 'variation %isolation%' . $num,
                'weight'    => ($num + 1),
                'quantity_and_stock_status' => array(
                    'qty' => ($num + 1) * 100,
                ),
            );
        }
        return $data;
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
     *
     * @return $this|ConfigurableProduct
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateConfigurable($this);

        return $this;
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
            'grid_filter' => array('name')
        );
        $this->_data = array(
            'fields' => array(
                'name' => array(
                    'value' => 'Configurable Product %isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'sku' => array(
                    'value' => 'configurable_sku_%isolation%',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'price' => array(
                    'value' => '9.99',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'tax_class_id' => array(
                    'value' => 'Taxable Goods',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'weight' => array(
                    'value' => '1',
                    'group' => static::GROUP_PRODUCT_DETAILS
                ),
                'configurable_attributes_data' => $this->getConfigurableAttributeData('attribute1')
            ),
            'variations-matrix'                         => $this->getOptionVariations('attribute1'),
            'affect_configurable_product_attributes'    => 0,
            'new-variations-attribute-set-id'           => 4,
            'checkout' => array(
                'selections' => array(
                    'attribute1' => 'one'
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
