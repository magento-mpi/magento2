<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Checkout_Cart_Item_Renderer extends Magento_Checkout_Block_Cart_Item_Renderer
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        return Mage::helper('Magento_GiftCard_Helper_Catalog_Product_Configuration')
            ->prepareCustomOption($this->getItem(), $code);
    }

    /**
     * Get gift card option list
     *
     * @return array
     */
    protected function _getGiftcardOptions()
    {
        return Mage::helper('Magento_GiftCard_Helper_Catalog_Product_Configuration')
            ->getGiftcardOptions($this->getItem());
    }

    /**
     * Return gift card and custom options array
     *
     * @return array
     */
    public function getOptionList()
    {
        return Mage::helper('Magento_GiftCard_Helper_Catalog_Product_Configuration')
            ->getOptions($this->getItem());
    }
}
