<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;
use \Magento\Framework\Service\Data\Eav\AttributeValue;
use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity\ProductEntity\ConverterInterface;

class Converter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(\Magento\Catalog\Model\Product $product)
    {
        return [
            ProductLinkEntity::TYPE => $product->getTypeId(),
            ProductLinkEntity::ATTRIBUTE_SET_ID => $product->getAttributeSetId(),
            ProductLinkEntity::SKU => $product->getSku(),
            ProductLinkEntity::POSITION => $product->getPosition(),
            ProductLinkEntity::CUSTOM_ATTRIBUTES_KEY => [
                [AttributeValue::ATTRIBUTE_CODE => 'qty', AttributeValue::VALUE => $product->getQty()],
            ]
        ];
    }
}
