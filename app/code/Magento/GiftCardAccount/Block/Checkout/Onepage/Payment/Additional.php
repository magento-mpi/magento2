<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Checkout\Onepage\Payment;

class Additional extends \Magento\Framework\View\Element\Template
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_isScopePrivate = true;
    }

    /**
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * @return mixed
     */
    public function getAppliedGiftCardAmount()
    {
        return $this->getQuote()->getBaseGiftCardsAmountUsed();
    }

    /**
     * @return bool
     */
    public function isFullyPaidAfterApplication()
    {
        // TODO remove dependences to other modules
        if ($this->getQuote()->getBaseGrandTotal() > 0 ||
            $this->getQuote()->getCustomerBalanceAmountUsed() > 0 ||
            $this->getQuote()->getRewardPointsBalance() > 0
        ) {
            return false;
        }

        return true;
    }
}
