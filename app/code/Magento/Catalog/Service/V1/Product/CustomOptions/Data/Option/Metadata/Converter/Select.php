<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Converter;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;
use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ConverterInterface;

class Select implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(Option $option)
    {
        $output = [];
        foreach ($option->getMetadata() as $value) {
            $attributes = $value->getCustomAttributes();
            $valueItem = [
                Metadata::PRICE => $value->getPrice(),
                Metadata::PRICE_TYPE => $value->getPriceType(),
                Metadata::SKU => $value->getSku(),
            ];
            foreach ($attributes as $attribute) {
                $valueItem[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $output[] = $valueItem;
        }
        return ['values' => $output];
    }
}
