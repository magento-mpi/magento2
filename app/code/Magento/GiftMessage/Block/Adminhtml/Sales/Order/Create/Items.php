<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift message adminhtml sales order create items
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Block_Adminhtml_Sales_Order_Create_Items extends Magento_Adminhtml_Block_Template
{
    /**
     * Gift message message
     *
     * @var Magento_GiftMessage_Helper_Message
     */
    protected $_giftMessageMessage = null;

    /**
     * @param Magento_GiftMessage_Helper_Message $giftMessageMessage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftMessage_Helper_Message $giftMessageMessage,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftMessageMessage = $giftMessageMessage;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get order item
     *
     * @return Magento_Sales_Model_Quote_Item
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
        return $this->_giftMessageMessage->getIsMessagesAvailable(
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
            $model = $this->_giftMessageMessage->getGiftMessage($this->getItem()->getGiftMessageId());
            return $this->escapeHtml($model->getMessage());
        }
        return '';
    }
}
