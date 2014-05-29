<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity;

class DefaultConverter implements ConverterInterface
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
            ProductLinkEntity::POSITION => $product->getPosition()
        ];
    }
}
