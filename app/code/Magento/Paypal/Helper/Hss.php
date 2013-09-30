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
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_Layout $layout
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_Layout $layout
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_layout = $layout;
        parent::__construct($context);
    }

    /**
     * Get template for button in order review page if HSS method was selected
     *
     * @param string $name template name
     * @param string $block buttons block name
     * @return string
     */
    public function getReviewButtonTemplate($name, $block)
    {
        $quote = $this->_checkoutSession->getQuote();
        if ($quote) {
            $payment = $quote->getPayment();
            if ($payment && in_array($payment->getMethod(), $this->_hssMethods)) {
                return $name;
            }
        }

        $blockObject = $this->_layout->getBlock($block);
        if ($blockObject) {
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
