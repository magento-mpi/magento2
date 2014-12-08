<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface;

class DefaultConverter implements ConverterInterface
{
    /**
     * Convert option data object value to array representation
     *
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return array
     */
    public function convert(\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option)
    {
        /** @var Metadata $value */
        $value = current($option->getMetadata());
        $output = [
            Metadata::PRICE => $value->getPrice(),
            Metadata::PRICE_TYPE => $value->getPriceType(),
            Metadata::SKU => $value->getSku(),
        ];

        foreach ($value->getCustomAttributes() as $attribute) {
            $output[$attribute->getAttributeCode()] = $attribute->getValue();
        }
        return $output;
    }
}
