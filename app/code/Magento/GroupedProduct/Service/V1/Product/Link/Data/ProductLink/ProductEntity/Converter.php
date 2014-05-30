<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;
use \Magento\Framework\Service\Data\Eav\AttributeValue;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(\Magento\Catalog\Model\Product $product)
    {
        return [
            ProductLink::TYPE => $product->getTypeId(),
            ProductLink::ATTRIBUTE_SET_ID => $product->getAttributeSetId(),
            ProductLink::SKU => $product->getSku(),
            ProductLink::POSITION => $product->getPosition(),
            ProductLink::CUSTOM_ATTRIBUTES_KEY => [
                [AttributeValue::ATTRIBUTE_CODE => 'qty', AttributeValue::VALUE => $product->getQty()],
            ]
        ];
    }
}
