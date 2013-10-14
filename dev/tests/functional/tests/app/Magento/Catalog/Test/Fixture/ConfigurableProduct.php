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
                        )
                    )
                )
            ),
        );

        //Default data set
        $this->switchData('configurable');
    }
}
