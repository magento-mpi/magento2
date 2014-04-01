<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class Product extends AbstractProduct
{
    /**
     * Reindex product permissions on product save
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterSave(\Magento\Catalog\Model\Product $product)
    {
        $this->reindex(array($product->getId()));
        return $product;
    }

    /**
     * Reindex product permissions on product delete
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterDelete(\Magento\Catalog\Model\Product $product)
    {
        $this->reindex(array($product->getId()));
        return $product;
    }
}
