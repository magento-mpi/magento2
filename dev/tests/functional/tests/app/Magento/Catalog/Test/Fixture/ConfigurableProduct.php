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
    protected $_attributes;
    /**
     * Custom constructor to create configurable product with attribute
     *
     * @param Config $configuration
     * @param array $placeholders
     */
    public function __construct(Config $configuration, $placeholders = array())
    {
        parent::__construct($configuration, $placeholders);
        $this->_placeholders['attribute1::getAttributeLabel'] = array($this, 'attributeProvider');
        $this->_placeholders['attribute1::getAttributeId'] = array($this, 'attributeProvider');
        $this->_placeholders['attribute1::getOptionFixtures'] = array($this, 'attributeProvider');
        $this->_placeholders['attribute1::getOptionVariations'] = array($this, 'attributeProvider');
    }

    public function reset()
    {
        $default = $this->_repository->get('default');

        $this->_dataConfig = $default['config'];
        $this->_data = $default['data'];
    }

    /**
     * Create new attribute
     *
     * @return string
     */
    protected function attributeProvider($placeholder)
    {
        list($code, $method) = explode('::', $placeholder);
        $attribute = $this->_getAttribute($code);
        if ($method == 'getOptionVariations') {
            return $this->_getOptionVariations($attribute->getAttributeOptionIds());
        } elseif ($method == 'getOptionFixtures') {
            return $this->_getOptionFixtures($attribute->getAttributeOptionIds());
        }
        return is_callable(array($attribute, $method)) ? $attribute->$method() : null;
    }

    protected function _getOptionVariations($optionIds)
    {
        $data = array();
        foreach ($optionIds as $num => $id) {
            $data[$id] = array(
                'name' => 'variation %isolation%',
                'sku' => 'variation %isolation%',
                'quantity_and_stock_status' => array(
                    'qty' => $num * 100,
                ),
            );
        }
    }

    protected function _getOptionFixtures($optionIds)
    {
        $data = array();
        foreach ($optionIds as $num => $id) {
            $data[$id] = array(
                'pricing_value' => $num*1,
                'is_percent' => 0,
                'include' => 1
            );
        }
    }

    protected function _getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $attribute = Factory::getFixtureFactory()->getMagentoCatalogProductAttribute();
            $attribute->switchData('configurable_attribute');
            $this->_attributes[$code] = $attribute->persist();
        }
        return $this->_attributes[$code];
    }

    /**
     * Create product
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateConfigurable($this);

        return $this;
    }

    /**
     * Get variations product prices
     *
     * @return array
     */
    public function getVariations()
    {
        return $this->_data['configurable_options']['configurable_items'];
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
                'configurable_attributes_data' => array
                (
                    '%attribute_id%' => array(
                        'label' => '%attribute1::getAttributeLabel%',
                        'values' => '%attribute1::getOptionFixtures%'
                    )
                ),
                'variations-matrix' => '%attribute1::getOptionVariations%'
            ),
            'affect_configurable_product_attributes' => 0,
            'new-variations-attribute-set-id' => 4
//            'configurable_options' => array(
//                'configurable_items' => array(
//                    array('product_price' => '1', 'product_quantity' => '100'),
//                    array('product_price' => '2', 'product_quantity' => '200')
//                ),
//            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogConfigurableProduct($this->_dataConfig, $this->_data);
    }
}