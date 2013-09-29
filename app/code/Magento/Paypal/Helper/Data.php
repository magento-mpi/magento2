<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Data helper
 */
class Magento_Paypal_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Cache for shouldAskToCreateBillingAgreement()
     *
     * @var bool
     */
    protected static $_shouldAskToCreateBillingAgreement = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * @var Magento_Sales_Model_Billing_AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Sales_Model_Billing_AgreementFactory $agreementFactory
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Sales_Model_Billing_AgreementFactory $agreementFactory
    ) {
        $this->_coreData = $coreData;
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($context);
    }

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param Magento_Paypal_Model_Config $config
     * @param int $customerId
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement(Magento_Paypal_Model_Config $config, $customerId)
    {
        if (null === self::$_shouldAskToCreateBillingAgreement) {
            self::$_shouldAskToCreateBillingAgreement = false;
            if ($customerId && $config->shouldAskToCreateBillingAgreement()) {
                if ($this->_agreementFactory->create()->needToCreateForCustomer($customerId)) {
                    self::$_shouldAskToCreateBillingAgreement = true;
                }
            }
        }
        return self::$_shouldAskToCreateBillingAgreement;
    }

    /**
     * Return backend config for element like JSON
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function getElementBackendConfig(Magento_Data_Form_Element_Abstract $element)
    {
        $config = $element->getFieldConfig();
        if (!array_key_exists('backend_congif', $config)) {
            return false;
        }

        $config = $config['backend_congif'];
        if (isset($config['enable_for_countries'])) {
            $config['enable_for_countries'] = explode(',', str_replace(' ', '', $config['enable_for_countries']));
        }
        if (isset($config['disable_for_countries'])) {
            $config['disable_for_countries'] = explode(',', str_replace(' ', '', $config['disable_for_countries']));
        }
        return $this->_coreData->jsonEncode($config);
    }
}
