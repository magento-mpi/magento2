<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;
use \Magento\Framework\Service\Data\Eav\AttributeValue;

class Converter
{
    /**
     * Convert product collection to array representation
     *
     * @param \Magento\Catalog\Model\Product[] $products
     * @return array
     */
    public function convert(array $products)
    {
        $output = [];
        foreach ($products as $item) {
            /** @var \Magento\Catalog\Model\Product $item */
            $output[] = [
                ProductLinkEntity::ID => $item->getId(),
                ProductLinkEntity::TYPE => $item->getTypeId(),
                ProductLinkEntity::ATTRIBUTE_SET_ID => $item->getAttributeSetId(),
                ProductLinkEntity::SKU => $item->getSku(),
                ProductLinkEntity::POSITION => $item->getPosition(),
                ProductLinkEntity::CUSTOM_ATTRIBUTES_KEY => [
                    [AttributeValue::ATTRIBUTE_CODE => 'qty', AttributeValue::VALUE => $item->getQty()],
                ]
            ];
        }
        return $output;
    }
} 
