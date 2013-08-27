<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Standard payment "form"
 */
class Magento_Paypal_Block_Express_Form extends Magento_Paypal_Block_Standard_Form
{
    /**
     * Payment method code
     * @var string
     */
    protected $_methodCode = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Paypal data
     *
     * @var Magento_Paypal_Helper_Data
     */
    protected $_paypalData = null;

    /**
     * @param Magento_Paypal_Helper_Data $paypalData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Paypal_Helper_Data $paypalData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_paypalData = $paypalData;
        parent::__construct($context, $data);
    }

    /**
     * Set template and redirect message
     */
    protected function _construct()
    {
        $result = parent::_construct();
        $this->setRedirectMessage(__('You will be redirected to the PayPal website.'));
        return $result;
    }

    /**
     * Set data to block
     *
     * @return Magento_Core_Block_Abstract
     */
    protected function _beforeToHtml()
    {
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
        if ($this->_paypalData->shouldAskToCreateBillingAgreement($this->_config, $customerId)
             && $this->canCreateBillingAgreement()) {
            $this->setCreateBACode(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        }
        return parent::_beforeToHtml();
    }
}
