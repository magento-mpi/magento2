<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Data\LinkedProductEntity\CollectionProvider;

class Upsell implements \Magento\Catalog\Service\V1\Data\LinkedProductEntity\CollectionProviderInterface
{
    /**
     * Get linked products
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $product->getUpSellProducts();
    }
}
