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
     * Indicates that block can display gift message form
     *
     * @return bool
     */
    public function canDisplayGiftmessageForm()
    {
        $quote = Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getQuote();
        return $this->helper('Magento_GiftMessage_Helper_Message')->getIsMessagesAvailable('items', $quote, $quote->getStore());
    }
}
