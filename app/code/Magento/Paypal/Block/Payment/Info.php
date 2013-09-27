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
 * PayPal common payment info block
 * Uses default templates
 */
class Magento_Paypal_Block_Payment_Info extends Magento_Payment_Block_Info_Cc
{
    /**
     * @var Magento_Paypal_Model_InfoFactory
     */
    protected $_paypalInfoFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Payment_Model_Config $paymentConfig
     * @param Magento_Paypal_Model_InfoFactory $paypalInfoFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Payment_Model_Config $paymentConfig,
        Magento_Paypal_Model_InfoFactory $paypalInfoFactory,
        array $data = array()
    ) {
        $this->_paypalInfoFactory = $paypalInfoFactory;
        parent::__construct($coreData, $context, $storeManager, $locale, $paymentConfig, $data);
    }

    /**
     * Don't show CC type for non-CC methods
     *
     * @return string|null
     */
    public function getCcTypeName()
    {
        if (Magento_Paypal_Model_Config::getIsCreditCardMethod($this->getInfo()->getMethod())) {
            return parent::getCcTypeName();
        }
    }

    /**
     * Prepare PayPal-specific payment information
     *
     * @param Magento_Object|array $transport
     * @return \Magento_Object
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        $transport = parent::_prepareSpecificInformation($transport);
        $payment = $this->getInfo();
        $paypalInfo = $this->_paypalInfoFactory->create();
        if (!$this->getIsSecureMode()) {
            $info = $paypalInfo->getPaymentInfo($payment, true);
        } else {
            $info = $paypalInfo->getPublicPaymentInfo($payment, true);
        }
        return $transport->addData($info);
    }
}
