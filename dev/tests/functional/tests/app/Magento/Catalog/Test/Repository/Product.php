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

        $this->_data['simple_required'] = $this->_data['default'];
        $this->_data['simple'] = $this->_data['default'];
        $this->_data['simple']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['simple_advanced_inventory'] = $this->getSimpleAdvancedInventory();
    }

    /**
     * Get simple product with advanced inventory
     *
     * @return array
     */
    protected function getSimpleAdvancedInventory()
    {
        $inventory = array(
            'data' => array(
                'fields' => array(
                    'inventory_manage_stock' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                    ),
                    'inventory_qty' => array(
                        'value' => 1,
                        'group' => 'product_info_tabs_advanced-inventory'
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $inventory);
        unset($product['data']['fields']['qty']);

        return $product;
    }
}
