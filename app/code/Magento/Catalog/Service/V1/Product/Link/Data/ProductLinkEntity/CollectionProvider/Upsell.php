<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity\CollectionProvider;

class Upsell implements \Magento\Catalog\Service\V1\Product\Link\Data\ProductLinkEntity\CollectionProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        return $product->getUpSellProducts();
    }
}
