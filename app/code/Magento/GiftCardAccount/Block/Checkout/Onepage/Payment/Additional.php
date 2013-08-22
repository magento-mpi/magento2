<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCardAccount_Block_Checkout_Onepage_Payment_Additional extends Magento_Core_Block_Template
{
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    public function getAppliedGiftCardAmount()
    {
        return $this->getQuote()->getBaseGiftCardsAmountUsed();
    }

    public function isFullyPaidAfterApplication()
    {
        // TODO remove dependences to other modules
        if ($this->getQuote()->getBaseGrandTotal() > 0 || $this->getQuote()->getCustomerBalanceAmountUsed() > 0 || $this->getQuote()->getRewardPointsBalance() > 0) {
            return false;
        }

        return true;
    }
}
