<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Carrier_Pickup
    extends Magento_Shipping_Model_Carrier_Abstract
    implements Magento_Shipping_Model_Carrier_Interface
{

    /**
     * @var string
     */
    protected $_code = 'pickup';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var Magento_Shipping_Model_Rate_ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var Magento_Shipping_Model_Rate_Result_MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory
     * @param Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Shipping_Model_Rate_Result_ErrorFactory $rateErrorFactory,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Shipping_Model_Rate_ResultFactory $rateResultFactory,
        Magento_Shipping_Model_Rate_Result_MethodFactory $rateMethodFactory,
        array $data = array()
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($coreStoreConfig, $rateErrorFactory, $logAdapterFactory, $data);
    }

    /**
     * @param Magento_Shipping_Model_Rate_Request $request
     * @return Magento_Shipping_Model_Rate_Result
     */
    public function collectRates(Magento_Shipping_Model_Rate_Request $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var Magento_Shipping_Model_Rate_Result $result */
        $result = $this->_rateResultFactory->create();

        if (!empty($rate)) {
            /** @var Magento_Shipping_Model_Rate_Result_Method $method */
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier('pickup');
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod('store');
            $method->setMethodTitle(__('Store Pickup'));

            $method->setPrice(0);
            $method->setCost(0);

            $result->append($method);
        }

        return $result;
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array('pickup'=>__('Store Pickup'));
    }

}
