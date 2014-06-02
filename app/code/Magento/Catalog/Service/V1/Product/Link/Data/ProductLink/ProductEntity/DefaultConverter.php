<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\ProductEntity;

use \Magento\Catalog\Service\V1\Product\Link\Data\ProductLink;

class DefaultConverter implements ConverterInterface
{
    /**
     * {@inheritdoc}
     */
    public function convert(\Magento\Catalog\Model\Product $product)
    {
        return [
            ProductLink::TYPE => $product->getTypeId(),
            ProductLink::SKU => $product->getSku(),
            ProductLink::POSITION => $product->getPosition()
        ];
    }
}
