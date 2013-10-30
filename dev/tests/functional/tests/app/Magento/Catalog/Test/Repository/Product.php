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

namespace Magento\Catalog\Test\Repository;

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Product extends AbstractRepository
{
    /**
     * {inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['simple_with_category'] = $this->_data['simple'];
        $this->_data['simple_with_category']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['simple_with_category']['data']['fields']['category_ids'] = array(
            'value' => array('%category::getCategoryId%')
        );

        $this->_data['simple_advanced_inventory'] = $this->_data['simple_with_category'];
        unset($this->_data['simple_advanced_inventory']['data']['fields']['qty']);
        $this->_data['simple_advanced_inventory']['data']['fields']['inventory_manage_stock'] = array(
            'value' => 'Yes',
            'input_value' => '1',
            'group' => 'product_info_tabs_advanced-inventory',
            'input' => 'select'
        );
        $this->_data['simple_advanced_inventory']['data']['fields']['inventory_qty'] = array(
            'value' => 1,
            'group' => 'product_info_tabs_advanced-inventory'
        );
    }
}
