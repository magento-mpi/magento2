<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Block\Checkout\Payment\Review;

class Container extends \Magento\View\Block\Template
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $coreData, $data);
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
            if ($payment->getMethodInstance()->getIsDeferred3dCheck()) {
                $this->setMethodCode($payment->getMethod());
                return parent::_toHtml();
            }
        }

        return '';
    }
}
