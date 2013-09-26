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
 * Multishipping cart link
 */
class Magento_Checkout_Block_Multishipping_Link extends Magento_Core_Block_Template
{
    /**
     * Checkout data
     *
     * @var Magento_Checkout_Helper_Data
     */
    protected $_checkoutData;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @param Magento_Checkout_Helper_Data $checkoutData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Helper_Data $checkoutData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutData = $checkoutData;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }

    /**
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->_checkoutData->isMultishippingCheckoutAvailable()) {
            return '';
        }
        return parent::_toHtml();
    }
}
