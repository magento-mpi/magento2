<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart product configuration model
 */
namespace Magento\Catalog\Model\Product;

class CartConfiguration
{
    /**
     * Decide whether product has been configured for cart or not
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     * @return bool
     */
    public function isProductConfigured(\Magento\Catalog\Model\Product $product, $config)
    {
        // If below POST fields were submitted - this is product's options, it has been already configured
        switch ($product->getTypeId()) {
            case \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE:
            case \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL:
                return isset($config['options']);
            case \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE:
                return isset($config['super_attribute']);
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                return isset($config['bundle_option']);
            case \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD:
                return isset($config['giftcard_amount']);
            case \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE:
                return isset($config['links']);
        }
        return false;
    }
}
