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
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param Magento_Payment_Model_Method_Abstract|null
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
        if ($method = $this->getQuote()->getPayment()->getMethod()) {
            return $method;
        }
        return false;
    }

    /**
     * Payment method form html getter
     * @param Magento_Payment_Model_Method_Abstract $method
     */
    public function getPaymentMethodFormHtml(Magento_Payment_Model_Method_Abstract $method)
    {
         return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page
     *
     * @param Magento_Payment_Model_Method_Abstract $method
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
        if ($form = $this->getChildBlock('payment.method.' . $method->getCode())) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
