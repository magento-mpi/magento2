<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Config extends Magento_Object
{
    /**
     * Shipping origin settings
     */
    const XML_PATH_ORIGIN_COUNTRY_ID = 'shipping/origin/country_id';
    const XML_PATH_ORIGIN_REGION_ID  = 'shipping/origin/region_id';
    const XML_PATH_ORIGIN_CITY       = 'shipping/origin/city';
    const XML_PATH_ORIGIN_POSTCODE   = 'shipping/origin/postcode';

    protected static $_carriers;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Shipping_Model_Carrier_Factory
     */
    protected $_carrierFactory;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Shipping_Model_Carrier_Factory $carrierFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Shipping_Model_Carrier_Factory $carrierFactory,
        array $data = array()
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_carrierFactory = $carrierFactory;
        parent::__construct($data);
    }

    /**
     * Retrieve active system carriers
     *
     * @param   mixed $store
     * @return  array
     */
    public function getActiveCarriers($store = null)
    {
        $carriers = array();
        $config = $this->_coreStoreConfig->getConfig('carriers', $store);
        foreach (array_keys($config) as $carrierCode) {
            if ($this->_coreStoreConfig->getConfigFlag('carriers/' . $carrierCode . '/active', $store)) {
                $carrierModel = $this->_getCarrier($carrierCode, $store);
                if ($carrierModel) {
                    $carriers[$carrierCode] = $carrierModel;
                }
            }
        }
        return $carriers;
    }

    /**
     * Retrieve all system carriers
     *
     * @param   mixed $store
     * @return  array
     */
    public function getAllCarriers($store = null)
    {
        $carriers = array();
        $config = $this->_coreStoreConfig->getConfig('carriers', $store);
        foreach (array_keys($config) as $carrierCode) {
            $model = $this->_getCarrier($carrierCode, $store);
            if ($model) {
                $carriers[$carrierCode] = $model;
            }
        }
        return $carriers;
    }

    /**
     * Retrieve carrier model instance by carrier code
     *
     * @param   string $carrierCode
     * @param   mixed $store
     * @return  Magento_Usa_Model_Shipping_Carrier_Abstract
     */
    public function getCarrierInstance($carrierCode, $store = null)
    {
        return $this->_getCarrier($carrierCode, $store);
    }

    /**
     * Get carrier model object
     *
     * @param $carrierCode
     * @param mixed $store
     * @return Magento_Shipping_Model_Carrier_Abstract
     */
    protected function _getCarrier($carrierCode, $store = null)
    {
        $carrier = $this->_carrierFactory->create($carrierCode, $store);
        self::$_carriers[$carrierCode] = $carrier;
        return self::$_carriers[$carrierCode];
    }
}
