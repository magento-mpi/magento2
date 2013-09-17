<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Sole Solution helper
 */
class Magento_Paypal_Helper_Hss extends Magento_Core_Helper_Abstract
{
    /**
     * Hosted Sole Solution methods
     *
     * @var array
     */
    protected $_hssMethods = array(
        Magento_Paypal_Model_Config::METHOD_HOSTEDPRO,
        Magento_Paypal_Model_Config::METHOD_PAYFLOWLINK,
        Magento_Paypal_Model_Config::METHOD_PAYFLOWADVANCED
    );

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        if ($blockObject = Mage::app()->getLayout()->getBlock($block)) {
            return $blockObject->getTemplate();
        }

        return '';
    }

    /**
     * Get methods
     *
     * @return array
     */
    public function getHssMethods()
    {
        return $this->_hssMethods;
    }
}
