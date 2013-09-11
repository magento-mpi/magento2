<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Adminhtml\Sales\Order\Creditmemo;

class Controls
 extends \Magento\Core\Block\Template
{
    public function canRefundToCustomerBalance()
    {
        if (!\Mage::registry('current_creditmemo')->getGiftCardsAmount()) {
            return false;
        }

        if (\Mage::registry('current_creditmemo')->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return true;
    }
}
