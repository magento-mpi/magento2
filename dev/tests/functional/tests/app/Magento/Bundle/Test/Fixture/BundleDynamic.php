<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Fixture;

use Mtf\Factory\Factory;

/**
 * Class BundleDynamic
 * Fixture for Bundle dynamic
 */
class BundleDynamic extends Bundle
{
    /**
     * Initialize fixture data
     */
    protected function _initData()
    {
        $this->_data['checkout'] = array(
            'prices' => array(
                'price_from' => 10,
                'price_to' => 15
            ),
            'selection' => array(
                'bundle_item_0' => 'assigned_product_0'
            )
        );
        parent::_initData();
        $this->_data['fields'] = array_merge_recursive(
            $this->_data['fields'],
            array(
                'sku_type' => array(
                    'value' => 'Dynamic',
                    'input_value' => '0',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'price_type' => array(
                    'value' => 'Dynamic',
                    'input_value' => '0',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'weight_type' => array(
                    'value' => 'Dynamic',
                    'input_value' => '0',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'product_website_1' => array(
                    'value' => 'Yes',
                    'input_value' => array(1),
                    'group' => static::GROUP_PRODUCT_WEBSITE,
                    'input' => 'checkbox',
                    'input_name' => 'website_ids'
                ),
                'shipment_type' => array(
                    'value' => 'Separately',
                    'input_value' => '1',
                    'group' => static::GROUP_PRODUCT_DETAILS,
                    'input' => 'select'
                ),
                'bundle_selections' => array(
                    'value' => array(
                        'bundle_options' => [
                            [
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
                                    [
                                        'search_data' => array(
                                            'name' => '%item1_simple1::getName%',
                                        ),
                                        'data' => array(
                                            'selection_qty' => array(
                                                'value' => 1
                                            ),
                                            'product_id' => array(
                                                'value' => '%item1_simple1::getProductId%'
                                            )
                                        )
                                    ],
                                    [
                                        'search_data' => array(
                                            'name' => '%item1_virtual2::getName%',
                                        ),
                                        'data' => array(
                                            'selection_qty' => array(
                                                'value' => 1
                                            ),
                                            'product_id' => array(
                                                'value' => '%item1_virtual2::getProductId%'
                                            )
                                        )
                                    ]
                                )
                            ]
                        ]
                    ),
                    'group' => static::GROUP
                )
            )
        );
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
