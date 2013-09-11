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
    public function getQuote()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session')->getQuote();
    }

    /**
     * Check payment method model
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod|null
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
     * @param \Magento\Payment\Model\Method\AbstractMethod $method
     */
    public function getPaymentMethodFormHtml(\Magento\Payment\Model\Method\AbstractMethod $method)
    {
         return $this->getChildHtml('payment.method.' . $method->getCode());
    }

    /**
     * Return method title for payment selection page
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $method
     */
    public function getMethodTitle(\Magento\Payment\Model\Method\AbstractMethod $method)
    {
        $form = $this->getChildBlock('payment.method.' . $method->getCode());
        if ($form && $form->hasMethodTitle()) {
            return $form->getMethodTitle();
        }
        return $method->getTitle();
    }

    /**
     * Payment method additional label part getter
     * @param \Magento\Payment\Model\Method\AbstractMethod $method
     */
    public function getMethodLabelAfterHtml(\Magento\Payment\Model\Method\AbstractMethod $method)
    {
        if ($form = $this->getChildBlock('payment.method.' . $method->getCode())) {
            return $form->getMethodLabelAfterHtml();
        }
    }
}
