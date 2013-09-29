<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales Billing Agreement Payment Method Abstract model
 */
abstract class Magento_Sales_Model_Payment_Method_Billing_AgreementAbstract
    extends Magento_Payment_Model_Method_Abstract
{
    /**
     * Transport billing agreement id
     */
    const TRANSPORT_BILLING_AGREEMENT_ID = 'ba_agreement_id';
    const PAYMENT_INFO_REFERENCE_ID      = 'ba_reference_id';

    protected $_infoBlockType = 'Magento_Sales_Block_Payment_Info_Billing_Agreement';
    protected $_formBlockType = 'Magento_Sales_Block_Payment_Form_Billing_Agreement';

    /**
     * Is method instance available
     *
     * @var null|bool
     */
    protected $_isAvailable = null;

    /**
     * @var Magento_Sales_Model_Billing_AgreementFactory
     */
    protected $_agreementFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Store_ConfigInterface $coreStoreConfig
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Sales_Model_Billing_AgreementFactory $agreementFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Store_ConfigInterface $coreStoreConfig,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Sales_Model_Billing_AgreementFactory $agreementFactory,
        array $data = array()
    ) {
        $this->_agreementFactory = $agreementFactory;
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $logAdapterFactory, $data);
    }

    /**
     * Check whether method is available
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (is_null($this->_isAvailable)) {
            if (is_object($quote) && $quote->getCustomer()) {
                $availableBA = $this->_agreementFactory->create()->getAvailableCustomerBillingAgreements(
                    $quote->getCustomer()->getId()
                );
                $isAvailableBA = count($availableBA) > 0;
                $this->_canUseCheckout = $this->_canUseInternal = $isAvailableBA;
            }
            $this->_isAvailable = parent::isAvailable($quote) && $this->_isAvailable($quote);
            $this->_canUseCheckout = ($this->_isAvailable && $this->_canUseCheckout);
            $this->_canUseInternal = ($this->_isAvailable && $this->_canUseInternal);
        }
        return $this->_isAvailable;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Magento_Payment_Model_Info
     */
    public function assignData($data)
    {
        $result = parent::assignData($data);

        $key = self::TRANSPORT_BILLING_AGREEMENT_ID;
        $id = false;
        if (is_array($data) && isset($data[$key])) {
            $id = $data[$key];
        } elseif ($data instanceof Magento_Object && $data->getData($key)) {
            $id = $data->getData($key);
        }
        if ($id) {
            $info = $this->getInfoInstance();
            $ba = $this->_agreementFactory->create()->load($id);
            if ($ba->getId() && $ba->getCustomerId() == $info->getQuote()->getCustomer()->getId()) {
                $info->setAdditionalInformation($key, $id)
                    ->setAdditionalInformation(self::PAYMENT_INFO_REFERENCE_ID, $ba->getReferenceId());
            }
        }
        return $result;
    }

    /**
     * @param object $quote
     */
    abstract protected function _isAvailable($quote);
}
