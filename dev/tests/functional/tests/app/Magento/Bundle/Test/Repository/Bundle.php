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

use Mtf\Repository\AbstractRepository;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Bundle extends AbstractRepository
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
        $this->_data['bundle'] = array(
            'config' => $defaultConfig,
            'data' => $defaultData
        );

        $this->_data['bundle_fixed_with_category'] = $this->_data['default'];
        $this->_data['bundle_fixed_with_category']['data']['category_name'] = '%category::getCategoryName%';
        $this->_data['bundle_fixed_with_category']['data']['fields']['category_ids'] = array(
            'value' => array('%category::getCategoryId%')
        );
        $this->_data['bundle_option_price'] = $this->_getBundleWithCustomPrice();

    }

    protected function _getBundleWithCustomPrice()
    {
        $data = $this->_data['default'];
        $data['data']['fields']['bundle_selections']['value']['bundle_item_0']['assigned_products']['assigned_product_1']['data']['selection_price_value']['value'] = 10;
        return $data;
    }
}
