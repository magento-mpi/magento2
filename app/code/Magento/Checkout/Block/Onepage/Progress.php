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
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Block\Onepage;

class Progress extends \Magento\Checkout\Block\Onepage\AbstractOnepage
{
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }

    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }

    public function getShippingMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }

    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }

    public function getShippingAmount()
    {
        /*$amount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $filter = \Mage::app()->getStore()->getPriceFilter();
        return $filter->filter($amount);*/
        //return $this->helper('Magento\Checkout\Helper\Data')->formatPrice(
        //    $this->getQuote()->getShippingAddress()->getShippingAmount()
        //);
        return $this->getQuote()->getShippingAddress()->getShippingAmount();
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Get is step completed. if is set 'toStep' then all steps after him is not completed.
     *
     * @param string $currentStep
     *  @see: \Magento\Checkout\Block\Onepage\AbstractOnepage::_getStepCodes() for allowed values
     * @return bool
     */
    public function isStepComplete($currentStep)
    {
        $stepsRevertIndex = array_flip($this->_getStepCodes());

        $toStep = $this->getRequest()->getParam('toStep');

        if (empty($toStep) || !isset($stepsRevertIndex[$currentStep])) {
            return $this->getCheckout()->getStepData($currentStep, 'complete');
        }

        if ($stepsRevertIndex[$currentStep] > $stepsRevertIndex[$toStep]) {
            return false;
        }

        return $this->getCheckout()->getStepData($currentStep, 'complete');
    }

    /**
     * Get quote shipping price including tax
     * @return float
     */
    public function getShippingPriceInclTax()
    {
        $inclTax = $this->getQuote()->getShippingAddress()->getShippingInclTax();
        return $this->formatPrice($inclTax);
    }

    public function getShippingPriceExclTax()
    {
        return $this->formatPrice($this->getQuote()->getShippingAddress()->getShippingAmount());
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }
}
