<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart item render block
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Cart\Item\Renderer;

class Grouped extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    const GROUPED_PRODUCT_IMAGE = 'checkout/cart/grouped_product_image';
    const USE_PARENT_IMAGE      = 'parent';

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
     * Get product thumbnail image
     *
     * @return \Magento\Catalog\Model\Product\Image
     */
    public function getProductThumbnail()
    {
        $product = $this->getProduct();
        if (!$product->getData('thumbnail')
            ||($product->getData('thumbnail') == 'no_selection')
            || (\Mage::getStoreConfig(self::GROUPED_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)) {
            $product = $this->getGroupedProduct();
        }
        return $this->helper('\Magento\Catalog\Helper\Image')->init($product, 'thumbnail');
    }

    /**
     * Prepare item html
     *
     * This method uses renderer for real product type
     *
     * @return string
     */
    protected function _toHtml()
    {
        $renderer = $this->getRenderedBlock()->getItemRenderer($this->getItem()->getRealProductType());
        $renderer->setItem($this->getItem());
//        $renderer->overrideProductUrl($this->getProductUrl());
        $renderer->overrideProductThumbnail($this->getProductThumbnail());
        $rendererHtml = $renderer->toHtml();
//        $renderer->overrideProductUrl(null);
        $renderer->overrideProductThumbnail(null);
        return $rendererHtml;
    }
}
