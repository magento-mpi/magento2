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
use \Magento\Catalog\Test\Fixture\Product;

/**
 * Class ConfigurableProduct
 * Configurable product data
 *
 * @package Magento\Catalog\Test\Fixture
 */
class ConfigurableProduct extends Product
{
    /**
     * Create product
     */
    public function persist()
    {
        Factory::getApp()->magentoCatalogCreateProduct($this);

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

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        $this->_repository = array(
            'configurable' => array(
                'config' => array(
                    'constraint' => 'Success',

                    'create_url_params' => array(
                        'type' => 'configurable',
                        'set' => 4,
                    ),

                    'grid_filter' => array('name')
                ),

                'data' => array(
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
                        )
                    ),
                    'configurable_options' => array(
                        'configurable_items' => array(
                            array('product_price' => '1.00', 'product_quantity' => '100'),
                            array('product_price' => '2.00', 'product_quantity' => '200')
                        ),
                    )
                )
            )
        );
        $this->_repository['configurable_default_category'] = $this->_repository['configurable'];
        $this->_repository['configurable_default_category']['data']['category_name'] = '%category%';

        //Default data set
        $this->switchData('configurable');
    }
}