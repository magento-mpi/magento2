<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs payment block
 *
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Checkout\Payment\Review;

class Container extends \Magento\Framework\View\Element\Template
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Custom rewrite for _toHtml() method
     * @return string
     */
    protected function _toHtml()
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            $method = $payment->getMethodInstance();
            if ($method->getIsDeferred3dCheck() || $method->getIsPendingOrderRequired()) {
                $this->setMethodCode($payment->getMethod());
                return parent::_toHtml();
            }
        }

        return '';
    }
}
