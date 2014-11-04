<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Api\Data;

/**
 * DataBuilder class for
 * \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
 */
class ProductCustomOptionOptionInterfaceDataBuilder extends \Magento\Framework\Service\Data\ExtensibleDataBuilder
{
    /**
     * @param string $productSku
     */
    public function setProductSku($productSku)
    {
        $this->data['product_sku'] = $productSku;
        return $this;
    }

    /**
     * @param int|null $optionId
     */
    public function setOptionId($optionId)
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
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
     * @param bool $isRequire
     */
    public function setIsRequire($isRequire)
    {
        $this->data['is_require'] = $isRequire;
        return $this;
    }

    /**
     * @param float|null $price
     */
    public function setPrice($price)
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * @param string|null $priceType
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
     * @param string|null $fileExtension
     */
    public function setFileExtension($fileExtension)
    {
        $this->data['file_extension'] = $fileExtension;
        return $this;
    }

    /**
     * @param int|null $maxCharacters
     */
    public function setMaxCharacters($maxCharacters)
    {
        $this->data['max_characters'] = $maxCharacters;
        return $this;
    }

    /**
     * @param int|null $imageSizeX
     */
    public function setImageSizeX($imageSizeX)
    {
        $this->data['image_size_x'] = $imageSizeX;
        return $this;
    }

    /**
     * @param int|null $imageSizeY
     */
    public function setImageSizeY($imageSizeY)
    {
        $this->data['image_size_y'] = $imageSizeY;
        return $this;
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface $values
     */
    public function setValues($values)
    {
        $this->data['values'] = $values;
        return $this;
    }

    /**
     * Initialize the builder
     *
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        parent::__construct($objectManager, 'Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface');
    }
}
