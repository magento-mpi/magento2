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

use Magento\Catalog\Test\Repository\Product;
use Magento\Catalog\Test\Fixture;

/**
 * Class Product Repository
 *
 * @package Magento\Catalog\Test\Repository
 */
class Bundle extends Product
{
    /**
     * @param string $productType
     * @return array
     */
    protected function resetRequiredFields($productType)
    {
        $required = parent::resetRequiredFields($productType);
        if (isset($this->_data[$productType]['data']['fields']['price'])) {
            $required = array_merge_recursive(
                $required,
                array(
                    'data' => array(
                        'fields' => array(
                            'price' => array(
                                'value' => 60,
                                'group' => Fixture\Product::GROUP_PRODUCT_DETAILS
                            )
                        ),
                        'checkout' => array(
                            'prices' => array(
                                'price_from' => 70,
                                'price_to' => 72
                            )
                        )
                    )
                )
            );
        } else {
            $required['data']['checkout']['prices'] = $this->_data[$productType]['data']['checkout']['prices'];
        }
        return $required;
    }
}
