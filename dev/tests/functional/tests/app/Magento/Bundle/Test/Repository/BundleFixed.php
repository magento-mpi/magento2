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

namespace Magento\Bundle\Test\Repository;

use Magento\Catalog\Test\Fixture;

/**
 * Class BundleFixed Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class BundleFixed extends Bundle
{
    /**
     * Construct
     *
     * @param array $defaultConfig
     * @param array $defaultData
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        parent::__construct($defaultConfig, $defaultData);

        $this->_data['bundle_fixed_selection_simple_product_percentage'] = array(
            'config' => $defaultConfig,
            'data' => $this->buildBundleWithSecondSelection($defaultData)
        );
    }

    /**
     * Build bundle product with 2 different items in the bundle. The second item will be selected.
     *
     * @param array $defaultData
     * @return array
     */
    protected function buildBundleWithSecondSelection(array $defaultData)
    {
        $data = $defaultData;

        /* select the second product */
        $data['checkout']['selection'] = array(
            'bundle_item_0' => 'assigned_product_1'
        );

        /* sets a simple product to the second item in the bundle */
        $data['fields']['bundle_selections']['value']['bundle_item_0']['assigned_products'] = array(
            'assigned_product_0' => array(
                'search_data' => array(
                    'name' => '%item1_virtual2::getProductName%',
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
                        'value' => '%item1_virtual2::getProductId%'
                    )
                )
            ),
            'assigned_product_1' => array(
                'search_data' => array(
                    'name' => '%item1_simple1::getProductName%',
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
                        'value' => '%item1_simple1::getProductId%'
                    )
                )
            )
        );

        return $data;
    }

}
