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

use Magento\Catalog\Test\Fixture;
use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Product extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $defaultConfig, array $defaultData)
    {
        $this->_data['default'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );
        $type = str_replace('product', '', strtolower(substr(get_class($this), strrpos(get_class($this), '\\') + 1)));
        $this->_data[$type . '_required'] = $this->_data['default'];
        $this->_data[$type] = $this->_data['default'];
        $this->_data[$type]['data']['category_name'] = '%category::getCategoryName%';
        $this->_data[$type]['data']['category_id'] = '%category::getCategoryId%';
        $this->_data[$type . '_edit_required_fields'] = $this->resetRequiredFields($type);
    }

    /**
     * @param string $productType
     * @return array
     */
    protected function resetRequiredFields($productType)
    {
        return array(
            'data' => array(
                'fields' => array(
                    'name' => array(
                        'value' => 'edited ' . $productType . ' %isolation%',
                        'group' => Fixture\Product::GROUP_PRODUCT_DETAILS
                    ),
                    'sku' => array(
                        'value' => 'edited ' . $productType . '_sku_%isolation%',
                        'group' => Fixture\Product::GROUP_PRODUCT_DETAILS
                    )
                )
            )
        );
    }

    /**
     * Get simple product with advanced inventory
     *
     * @return array
     */
    protected function _getSimpleOutOfStock()
    {
        $inventory = array(
            'data' => array(
                'fields' => array(
                    'inventory_manage_stock' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                    ),
                    'inventory_qty' => array(
                        'value' => 0,
                        'group' => Fixture\Product::GROUP_PRODUCT_INVENTORY
                    ),
                    'inventory_stock_availability' => array(
                        'value' => 'Out of Stock', // Out of Stock
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $inventory);
        unset($product['data']['fields']['qty']);
        return $product;
    }
}
