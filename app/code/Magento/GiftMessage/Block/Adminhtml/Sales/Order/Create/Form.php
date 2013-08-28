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
 * Adminhtml sales order create gift message form
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Block_Adminhtml_Sales_Order_Create_Form extends Magento_Adminhtml_Block_Template
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
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        $quote = Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getQuote();
        return $this->_giftMessageMessage->getIsMessagesAvailable('items', $quote, $quote->getStore());
    }
}
