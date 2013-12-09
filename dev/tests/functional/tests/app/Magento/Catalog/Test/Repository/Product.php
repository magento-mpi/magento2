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
     * {inheritdoc}
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
        $this->_data['simple_with_map'] = $this->getSimpleAppliedMAP($defaultData);
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
                        'group' => \Magento\Catalog\Test\Fixture\Product::GROUP_PRODUCT_DETAILS
                    ),
                    'sku' => array(
                        'value' => 'edited ' . $productType . '_sku_%isolation%',
                        'group' => \Magento\Catalog\Test\Fixture\Product::GROUP_PRODUCT_DETAILS
                    )
                )
            )
        );
    }

    /**
     * Get simple product with advanced pricing (MAP)
     *
     * @return array
     */
    protected function getSimpleAppliedMAP()
    {
        $pricing = array(
            'data' => array(
                'fields' => array(
                    'msrp_enabled' => array(
                        'value' => 'Yes',
                        'input_value' => '1',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING,
                        'input' => 'select'
                    ),
                    'msrp_display_actual_price_type' => array(
                        'value' => 'On Gesture',
                        'input_value' => '1',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING,
                        'input' => 'select'
                    ),
                    'msrp' => array(
                        'value' => '15',
                        'group' => Fixture\Product::GROUP_PRODUCT_PRICING
                    )
                )
            )
        );
        $product = array_replace_recursive($this->_data['simple'], $pricing);

        return $product;
    }
}
