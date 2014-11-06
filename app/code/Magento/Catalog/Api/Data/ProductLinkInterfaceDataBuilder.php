<?php
namespace Magento\Catalog\Api\Data;

/**
 * DataBuilder class for \Magento\Catalog\Api\Data\ProductLinkInterface
 */
class ProductLinkInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductLinkInterface');
    }

    /**
     * @param string $productSku
     */
    public function setProductSku($productSku)
    {
        $this->data['product_sku'] = $productSku;
        return $this;
    }

    /**
     * @param string $linkType
     */
    public function setLinkType($linkType)
    {
        $this->data['link_type'] = $linkType;
        return $this;
    }

    /**
     * @param string $linkedProductSku
     */
    public function setLinkedProductSku($linkedProductSku)
    {
        $this->data['linked_product_sku'] = $linkedProductSku;
        return $this;
    }

    /**
     * @param string $linkedProductType
     */
    public function setLinkedProductType($linkedProductType)
    {
        $this->data['linked_product_type'] = $linkedProductType;
        return $this;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->data = [];
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
