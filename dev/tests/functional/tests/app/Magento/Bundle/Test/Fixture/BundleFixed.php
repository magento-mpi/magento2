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
 * Class BundleFixed
 */
class BundleFixed extends Bundle
{
    /**
     * Initialize fixture data
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initData()
    {
        $this->_data['checkout'] = array(
            'prices' => array(
                'price_from' => 110,
                'price_to' => 120
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
                    'value' => 100,
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
                                            'name' => '%item1_simple1::getName%',
                                        ),
                                        'data' => array(
                                            'selection_price_value' => 10,
                                            'selection_price_type' => 'Fixed',
                                            'selection_qty' => 1,
                                            'product_id' => '%item1_simple1::getProductId%'
                                        )
                                    ),
                                    'assigned_product_1' => array(
                                        'search_data' => array(
                                            'name' => '%item1_virtual2::getName%',
                                        ),
                                        'data' => array(
                                            'selection_price_value' => 20,
                                            'selection_price_type' => 'Percent',
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
