<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Block\Checkout\Onepage\Payment;

class Additional extends \Magento\Core\Block\Template
{
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
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
