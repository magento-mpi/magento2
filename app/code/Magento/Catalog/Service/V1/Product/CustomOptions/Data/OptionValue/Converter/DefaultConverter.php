<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Converter;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;
use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ConverterInterface;

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
        /** @var OptionValue $value */
        $value = current($option->getValue());
        $output = [
            OptionValue::PRICE => $value->getPrice(),
            OptionValue::PRICE_TYPE => $value->getPriceType(),
            OptionValue::SKU => $value->getSku(),
        ];

        foreach ($value->getCustomAttributes() as $attribute) {
            $output[$attribute->getAttributeCode()] = $attribute->getValue();
        }
        return $output;
    }
}
