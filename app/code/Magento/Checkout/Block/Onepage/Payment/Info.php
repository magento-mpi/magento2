<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkout payment information data
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage\Payment;

class Info extends \Magento\Payment\Block\Info\AbstractContainer
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $paymentData, $data);
    }

    /**
     * Retrieve payment info model
     *
     * @return \Magento\Payment\Model\Info
     */
    public function getPaymentInfo()
    {
        $info = $this->_checkoutSession->getQuote()->getPayment();
        if ($info->getMethod()) {
            return $info;
        }
        return false;
    }

    protected function _toHtml()
    {
        $html = '';
        if ($block = $this->getChildBlock($this->_getInfoBlockName())) {
            $html = $block->toHtml();
        }
        return $html;
    }
}
