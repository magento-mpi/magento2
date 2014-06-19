<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\Converter;

use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;
use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;
use \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue\ConverterInterface;

class Select implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(Option $option)
    {
        $output = [];
        foreach ($option->getValue() as $value) {
            $attributes = $value->getCustomAttributes();
            $valueItem = [
                OptionValue::PRICE => $value->getPrice(),
                OptionValue::PRICE_TYPE => $value->getPriceType(),
                OptionValue::SKU => $value->getSku(),
            ];
            foreach ($attributes as $attribute) {
                $valueItem[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $output[] = $valueItem;
        }
        return ['values' => $output];
    }
}
