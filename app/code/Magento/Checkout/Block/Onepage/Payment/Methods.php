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
 * One page checkout status
 *
 * @category   Magento
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage\Payment;

class Methods extends \Magento\Payment\Block\Form\Container
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $paymentHelper, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * @return \Magento\Sales\Model\Quote|\Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\Method $method
     * @return bool
     */
    protected function _canUseMethod($method)
    {
        return $method && $method->canUseCheckout() && parent::_canUseMethod($method);
    }

    /**
     * Retrieve code of current payment method
     *
     * @return mixed
     */
    public function getSelectedMethodCode()
    {
        $method = $this->getQuote()->getPayment()->getMethod();
        if ($method) {
            return $method;
        }
        return false;
    }

    /**
     * Payment method form html getter
     * @param \Magento\Payment\Model\Method $method
     * @return string
     */
    public function getPaymentMethodFormHtml(\Magento\Payment\Model\Method $method)
    {
         return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page
     *
     * @param \Magento\Payment\Model\Method $method
     * @return string
     */
    public function getMethodTitle(\Magento\Payment\Model\Method $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }
        return $method->getTitle();
    }

    /**
     * Payment method additional label part getter
     * @param \Magento\Payment\Model\Method $method
     */
    public function getMethodLabelAfterHtml(\Magento\Payment\Model\Method $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
