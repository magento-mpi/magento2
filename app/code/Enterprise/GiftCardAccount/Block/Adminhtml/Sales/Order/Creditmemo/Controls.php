<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Adminhtml_Sales_Order_Creditmemo_Controls
 extends Magento_Core_Block_Template
{
    public function canRefundToCustomerBalance()
    {
        if (!Mage::registry('current_creditmemo')->getGiftCardsAmount()) {
            return false;
        }

        if (Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }
}
