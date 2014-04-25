<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Helper;

use \Magento\Catalog\Model\Product;

/**
 * Class Data
 * Helper class for getting options
 *
 * @package Magento\ConfigurableProduct\Helper
 */
class Data
{
    /**
     * Catalog Image Helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(\Magento\Catalog\Helper\Image $imageHelper)
    {
        $this->imageHelper = $imageHelper;
    }

    /**
     * Get Options for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = array();
        $baseImageUrl = (string)$this->imageHelper->init($currentProduct, 'image');

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $image = (string)$this->imageHelper->init($product, 'image');

            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['images'][$productAttributeId][$attributeValue][$productId] = $image ? $image : $baseImageUrl;
            }
        }

        return $options;
    }

    /**
     * Get allowed attributes
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return $product->getTypeInstance()->getConfigurableAttributes($product);
    }
}
