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

namespace Magento\Bundle\Test\Fixture;

use Mtf\System\Config;
use Mtf\Factory\Factory;
use Magento\Catalog\Test\Fixture\Product as Product;

class BundleFixed extends Bundle
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_data['fields'] = array(
            'name' => array(
                'value' => 'Bundle Fixed Product Required %isolation%',
                'group' => static::GROUP_PRODUCT_DETAILS
            ),
            'sku' => array(
                'value' => 'bundle_sku_fixed_%isolation%',
                'group' => static::GROUP_PRODUCT_DETAILS
            ),
            'sku_type' => array(
                'value' => 'Fixed',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'price_type' => array(
                'value' => 'Fixed',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'price' => array(
                'value' => '100',
                'group' => static::GROUP_PRODUCT_DETAILS
            ),
            'tax_class_id' => array(
                'value' => 'Taxable Goods',
                'input_value' => '2',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'weight_type' => array(
                'value' => 'Fixed',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'weight' => array(
                'value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS
            ),
            'shipment_type' => array(
                'value' => 'Separately',
                'input_value' => '1',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'bundle_selections' => array(
                'value' => array(
                    'bundle_item_1' => array(
                        'title' => array(
                            'value' => 'Drop-down Option'
                        ),
                        'type' => array(
                            'value' => 'Drop-down',
                            'input_value' => 'select'
                        ),
                        'required' => array(
                            'value' => 'Yes',
                            'input_value' => '1'
                        ),
                        'assigned_products' => array(
                            'assigned_product_2' => array(
                                'search_data' => array(
                                    'name' => '%item1_product1::getProductName%',
                                ),
                                'data' => array(
                                    'selection_price_value' => array(
                                        'value' => 10
                                    ),
                                    'selection_price_type' => array(
                                        'value' => 'Fixed',
                                        'input' => 'select',
                                        'input_value' => 0
                                    ),
                                    'selection_qty' => array(
                                        'value' => 1
                                    ),
                                    'product_id' => array(
                                        'value' => '%item1_product1::getProductId%'
                                    )
                                )
                            ),
                            'assigned_product_3' => array(
                                'search_data' => array(
                                    'name' => '%item1_product2::getProductName%',
                                ),
                                'data' => array(
                                    'selection_price_value' => array(
                                        'value' => 20
                                    ),
                                    'selection_price_type' => array(
                                        'value' => 'Percent',
                                        'input' => 'select',
                                        'input_value' => 1
                                    ),
                                    'selection_qty' => array(
                                        'value' => 1
                                    ),
                                    'product_id' => array(
                                        'value' => '%item1_product2::getProductId%'
                                    )
                                )
                            )
                        )
                    )
                ),
                'group' => static::GROUP_BUNDLE_OPTIONS
            )
        ) + $this->_data['fields'];
        $this->_data['checkout'] = array(
            'prices' => array(
                'price_from' => '110',
                'price_to' => '120'
            ),
            'selection' => array(
                'bundle_item_0' => 'assigned_product_0'
            )
        );

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
