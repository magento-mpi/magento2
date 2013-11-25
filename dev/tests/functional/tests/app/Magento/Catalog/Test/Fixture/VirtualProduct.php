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

use Mtf\System\Config;
use Mtf\Factory\Factory;

class VirtualProduct extends AbstractProduct
{

    /**
     * {inheritdoc}
     */
    protected function _initData()
    {
        parent::_initData();
        $this->_dataConfig = array(
            'constraint' => 'Success',

            'grid_filter'       => array('name'),

            'create_url_params' => array(
                'type' => 'virtual',
                'set'  => 4,
            ),
            'input_prefix' => 'product'
        );

        $data = array(
            'price'  => array(
                'value' => '10',
                'group' => static::GROUP_PRODUCT_DETAILS
            ),
            'tax_class_id' => array(
                'value' => 'Taxable Goods',
                'input_value' => '2',
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input' => 'select'
            ),
            'qty'    => array(
                'value' => 1000,
                'group' => static::GROUP_PRODUCT_DETAILS,
                'input_name'  => 'product[quantity_and_stock_status][qty]'
            ),
            'product_website_1' => array(
                'value' => 'Yes',
                'input_value' => 1,
                'group' => static::GROUP_PRODUCT_WEBSITE,
                'input' => 'checkbox',
                'input_name'  => 'product[website_ids][]'
            ),
            'inventory_manage_stock' => array(
                'value' => 'No',
                'input_value' => '0',
                'group' => static::GROUP_PRODUCT_INVENTORY,
                'input' => 'select',
                'input_name'  => 'product[stock_data][manage_stock]'
            )
        );

        $this->_data['fields'] = array_merge($this->_data['fields'], $data);

        $this->_repository = Factory::getRepositoryFactory()
            ->getMagentoCatalogVirtualProduct($this->_dataConfig, $this->_data);
    }
}
