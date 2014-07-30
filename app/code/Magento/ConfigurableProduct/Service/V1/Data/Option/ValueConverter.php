<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Data\Option;

class ValueConverter
{
    public function convertArrayFromData(Value $value)
    {
        return [
            'value_index' => $value->getIndex(),
            'is_percent' => $value->isPercent(),
            'pricing_value' => $value->getPrice(),
        ];
    }
}
