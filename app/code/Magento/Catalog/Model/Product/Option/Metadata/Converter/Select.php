<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Metadata\Converter;

use Magento\Catalog\Model\Product\Option\Metadata;
use Magento\Catalog\Model\Product\Option;
use \Magento\Catalog\Model\Product\Option\Metadata\ConverterInterface;

class Select implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option)
    {
        $output = [];
        foreach ($option->getMetadata() as $value) {
            $attributes = $value->getCustomAttributes();
            $valueItem = [
                Metadata::PRICE => $value->getPrice(),
                Metadata::PRICE_TYPE => $value->getPriceType(),
                Metadata::SKU => $value->getSku()
            ];
            foreach ($attributes as $attribute) {
                $valueItem[$attribute->getAttributeCode()] = $attribute->getValue();
            }
            $output[] = $valueItem;
        }
        return ['values' => $output];
    }
}
