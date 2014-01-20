<?php
/**
 * Plugin for product type configuration converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Model\ProductTypes\Config\Converter\Plugin;

class Grouped
{
    /**
     * Set value to product type configuration data that grouped product type is a set of products
     *
     * @param array $config
     * @return array
     */
    public function afterConvert(array $config)
    {
        if (isset($config[\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE])) {
            $config[\Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE]['is_product_set'] = true;
        }
        return $config;
    }
} 
