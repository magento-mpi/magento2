<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Block\Checkout\Cart\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return \Mage::helper('Magento\GiftCard\Helper\Catalog\Product\Configuration')
            ->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return \Mage::helper('Magento\GiftCard\Helper\Catalog\Product\Configuration')
            ->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return \Mage::helper('Magento\GiftCard\Helper\Catalog\Product\Configuration')
            ->getOptions($this->getItem());
    }
}
