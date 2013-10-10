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
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * @return \Magento\Sales\Model\Quote
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
