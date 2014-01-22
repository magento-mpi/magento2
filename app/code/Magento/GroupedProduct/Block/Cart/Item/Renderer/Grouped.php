<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Cart\Item\Renderer;

use Magento\Catalog\Model\Config\Source\Product\Thumbnail as ThumbnailSource;

/**
 * Shopping cart item render block
 */
class Grouped extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * Path in config to the setting which defines if parent or child product should be used to generate a thumbnail.
     */
    const CONFIG_THUMBNAIL_SOURCE = 'checkout/cart/grouped_product_image';

    /**
     * Get item grouped product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getGroupedProduct()
    {
        $option = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            return $option->getProduct();
        }
        return $this->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getProductForThumbnail()
    {
        /**
         * Show grouped product thumbnail if it must be always shown according to the related setting in system config
         * or if child product thumbnail is not available
         */
        if ($this->_storeConfig->getConfig(self::CONFIG_THUMBNAIL_SOURCE) == ThumbnailSource::OPTION_USE_PARENT_IMAGE
            || !($this->getProduct()->getThumbnail() && $this->getProduct()->getThumbnail() != 'no_selection')
        ) {
            $product = $this->getGroupedProduct();
        } else {
            $product = $this->getProduct();
        }
        return $product;
    }
}
