<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping adminhtml sales order create items
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Adminhtml_Sales_Order_Create_Link extends Magento_Adminhtml_Block_Template
{
    /**
     * Get order item from parent block
     *
     * @return Magento_Sales_Model_Order_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getItem();
    }

    /**
     * Get gift wrapping design
     *
     * @return string
     */
    public function getDesign()
    {
        if ($this->getItem()->getGwId()) {
            $wrappingModel = Mage::getModel('Magento_GiftWrapping_Model_Wrapping')->load($this->getItem()->getGwId());
            if ($wrappingModel->getId()) {
                return $this->escapeHtml($wrappingModel->getDesign());
            }
        }
        return '';
    }

    /**
     * Check ability to display gift wrapping for order items
     *
     * @return bool
     */
    public function canDisplayGiftWrappingForItem()
    {
        $product = $this->getItem()->getProduct();
        $allowed = !$product->getTypeInstance()->isVirtual($product) && $product->getGiftWrappingAvailable();
        $storeId = $this->getItem()->getStoreId();
        return Mage::helper('Magento_GiftWrapping_Helper_Data')->isGiftWrappingAvailableForProduct($allowed, $storeId);
    }
}
