<?php
namespace Magento\Catalog\Api\Data;

/**
 * DataBuilder class for
 * \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface
 */
class ProductCustomOptionValuesInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder($sortOrder)
    {
        $this->data['sort_order'] = $sortOrder;
        return $this;
    }

    /**
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * @param string $priceType
     */
    public function setPriceType($priceType)
    {
        $this->data['price_type'] = $priceType;
        return $this;
    }

    /**
     * @param string|null $sku
     */
    public function setSku($sku)
    {
        $this->data['sku'] = $sku;
        return $this;
    }

    /**
     * @param int|null $optionTypeId
     */
    public function setOptionTypeId($optionTypeId)
    {
        $this->data['option_type_id'] = $optionTypeId;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface');
    }
}
