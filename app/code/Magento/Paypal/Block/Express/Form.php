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
     *
     * @var string
     */
    protected $_methodCode = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Paypal data
     *
     * @var Magento_Paypal_Helper_Data
     */
    protected $_paypalData;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @param Magento_Paypal_Helper_Data $paypalData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Paypal_Model_ConfigFactory $paypalConfigFactory
     * @param Magento_Customer_Model_Session $customerSession
     * @param array $data
     */
    public function __construct(
        Magento_Paypal_Helper_Data $paypalData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Paypal_Model_ConfigFactory $paypalConfigFactory,
        Magento_Customer_Model_Session $customerSession,
        array $data = array()
    ) {
        $this->_paypalData = $paypalData;
        $this->_customerSession = $customerSession;
        parent::__construct($coreData, $context, $locale, $paypalConfigFactory, $data);
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
        $customerId = $this->_customerSession->getCustomerId();
        if ($this->_paypalData->shouldAskToCreateBillingAgreement($this->_config, $customerId)
            && $this->canCreateBillingAgreement()
        ) {
            $this->setCreateBACode(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
        }
        return parent::_beforeToHtml();
    }
}
