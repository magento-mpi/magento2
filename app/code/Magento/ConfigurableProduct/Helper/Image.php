<?php
/**
 * Created by PhpStorm.
 * User: tshevchenko
 * Date: 16.04.14
 * Time: 16:21
 */

namespace Magento\ConfigurableProduct\Helper;

use \Magento\Catalog\Model\Product;

/**
 * Class Image
 * Helper class for getting options images
 *
 * @package Magento\ConfigurableProduct\Helper
 */
class Image
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
     * Get Images for Configurable Product Options
     *
     * @param \Magento\Catalog\Model\Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptionsImage($currentProduct, $allowedProducts)
    {
        $options = array();
        $baseImageUrl = (string)$this->imageHelper->init($currentProduct, 'image');

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $this->imageHelper->init($product, 'image');

            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());
                if (!isset($options[$productAttributeId])) {
                    $options[$productAttributeId] = array();
                }

                if (!isset($options[$productAttributeId][$attributeValue])) {
                    $options[$productAttributeId][$attributeValue] = array();
                }
                $options[$productAttributeId][$attributeValue][] = $productId;

                if (!$product->getImage() || $product->getImage() === 'no_selection') {
                    $options['images'][$productAttributeId][$attributeValue][$productId] = $baseImageUrl;
                } else {
                    $options['images'][$productAttributeId][$attributeValue][$productId] = (string)$this->imageHelper;
                }
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
