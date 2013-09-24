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
    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    public function getAppliedGiftCardAmount()
    {
        return $this->getQuote()->getBaseGiftCardsAmountUsed();
    }

    public function isFullyPaidAfterApplication()
    {
        // TODO remove dependences to other modules
        if ($this->getQuote()->getBaseGrandTotal() > 0
            || $this->getQuote()->getCustomerBalanceAmountUsed() > 0
            || $this->getQuote()->getRewardPointsBalance() > 0
        ) {
            return false;
        }

        return true;
    }
}
