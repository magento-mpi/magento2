<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Placeholder container for catalog product lists
 */
namespace Magento\FullPageCache\Model\Container;

class CatalogProductList
    extends \Magento\FullPageCache\Model\Container\Advanced\Quote
{
    /**
     * Render block that was not cached
     *
     * @return false|string
     */
    protected function _renderBlock()
    {
        $productId = $this->_getProductId();
        if ($productId && !\Mage::registry('product')) {
            $product = \Mage::getModel('\Magento\Catalog\Model\Product')
                ->setStoreId(\Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product) {
                \Mage::register('product', $product);
            }
        }

        if (\Mage::registry('product')) {
            $block = $this->_getPlaceHolderBlock();
            \Mage::dispatchEvent('render_block', array('block' => $block, 'placeholder' => $this->_placeholder));
            return $block->toHtml();
        }

        return '';
    }
}
