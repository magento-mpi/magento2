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
class Magento_Checkout_Block_Onepage_Payment_Methods extends Magento_Payment_Block_Form_Container
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

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
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return Magento_Sales_Model_Quote|Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param Magento_Payment_Model_Method_Abstract $method
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
     * @param Magento_Payment_Model_Method_Abstract $method
     * @return string
     */
    public function getPaymentMethodFormHtml(Magento_Payment_Model_Method_Abstract $method)
    {
         return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page
     *
     * @param Magento_Payment_Model_Method_Abstract $method
     * @return string
     */
    public function getMethodTitle(Magento_Payment_Model_Method_Abstract $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }
        return $method->getTitle();
    }

    /**
     * Payment method additional label part getter
     * @param Magento_Payment_Model_Method_Abstract $method
     */
    public function getMethodLabelAfterHtml(Magento_Payment_Model_Method_Abstract $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
