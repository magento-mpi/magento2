<?php
namespace Magento\Catalog\Api\Data;

/**
 * Builder class for \Magento\Catalog\Api\Data\ProductLinkInterface
 */
class ProductLinkInterfaceBuilder extends \Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder
{
    /**
     * {@inheritdoc}
     */
    public function setProductSku($productSku)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkInterface::PRODUCT_SKU, $productSku);
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkType($linkType)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkInterface::LINK_TYPE, $linkType);
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkedProductSku($linkedProductSku)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkInterface::LINKED_PRODUCT_SKU, $linkedProductSku);
    }

    /**
     * {@inheritdoc}
     */
    public function setLinkedProductType($linkedProductType)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkInterface::LINKED_PRODUCT_TYPE, $linkedProductType);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($position)
    {
        $this->_set(\Magento\Catalog\Api\Data\ProductLinkInterface::POSITION, $position);
    }
}
