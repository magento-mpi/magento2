<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift message adminhtml sales order create items
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Block_Adminhtml_Sales_Order_Create_Items extends Magento_Adminhtml_Block_Template
{
    /**
     * Get order item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->getParentBlock()->getItem();
    }

    /**
     * Indicates that block can display gift messages form
     *
     * @return boolean
     */
    public function canDisplayGiftMessage()
    {
        $item = $this->getItem();
        if (!$item) {
            return false;
        }
        return $this->helper('Mage_GiftMessage_Helper_Message')->getIsMessagesAvailable(
            'item', $item, $item->getStoreId()
        );
    }

   /**
     * Return form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form')
            ->setEntity($this->getItem())
            ->setEntityType('item')
            ->toHtml();
    }

    /**
     * Retrieve gift message for item
     *
     * @return string
     */
    public function getMessageText()
    {
        if ($this->getItem()->getGiftMessageId()) {
            $model = $this->helper('Mage_GiftMessage_Helper_Message')->getGiftMessage($this->getItem()->getGiftMessageId());
            return $this->escapeHtml($model->getMessage());
        }
        return '';
    }
}
