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
                        'bundle_options' => array(
                            'bundle_item_0' => array(
                                'title' => 'Drop-down Option',
                                'type' => 'Drop-down',
                                'required' => 'Yes',
                                'assigned_products' => array(
                                    'assigned_product_0' => array(
                                        'search_data' => array(
                                            'name' => '%item1_simple1::getProductName%',
                                        ),
                                        'data' => array(
                                            'selection_qty' => 1,
                                            'product_id' => '%item1_simple1::getProductId%'
                                        )
                                    ),
                                    'assigned_product_1' => array(
                                        'search_data' => array(
                                            'name' => '%item1_virtual2::getProductName%',
                                        ),
                                        'data' => array(
                                            'selection_qty' => 1,
                                            'product_id' => '%item1_virtual2::getProductId%'
                                        )
                                    )
                                )
                            )
                        )
                    ),
                    'group' => static::GROUP
                )
            )
        );
        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoBundleBundle($this->_dataConfig, $this->_data);
    }
}
