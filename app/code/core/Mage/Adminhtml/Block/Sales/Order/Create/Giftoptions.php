<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml order create gift options block
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Giftoptions extends Mage_Backend_Block_Template
{
    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $helper Enterprise_GiftWrapping_Helper_Data*/
        $helper = Mage::helper('Enterprise_GiftWrapping_Helper_Data');
        if ((bool)$helper->allowGiftReceipt()
            || (bool)$helper->allowPrintedCard()
            || (bool)$helper->isGiftWrappingAvailableForOrder()
            || Mage::getStoreConfigFlag(Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ORDER)
        ) {
            return parent::_toHtml();
        }
        return '';
    }
}
