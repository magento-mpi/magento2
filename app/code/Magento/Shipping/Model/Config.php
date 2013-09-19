<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Shipping\Model;

class Config extends \Magento\Object
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
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_coreStoreConfig = $coreStoreConfig;
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
        foreach ($config as $code => $carrierConfig) {
            if ($this->_coreStoreConfig->getConfigFlag('carriers/'.$code.'/active', $store)) {
                $carrierModel = $this->_getCarrier($code, $carrierConfig, $store);
                if ($carrierModel) {
                    $carriers[$code] = $carrierModel;
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
        foreach ($config as $code => $carrierConfig) {
            $model = $this->_getCarrier($code, $carrierConfig, $store);
            if ($model) {
                $carriers[$code] = $model;
            }
        }
        return $carriers;
    }

    /**
     * Retrieve carrier model instance by carrier code
     *
     * @param   string $carrierCode
     * @param   mixed $store
     * @return  \Magento\Usa\Model\Shipping\Carrier\AbstractCarrier
     */
    public function getCarrierInstance($carrierCode, $store = null)
    {
        $carrierConfig =  $this->_coreStoreConfig->getConfig('carriers/'.$carrierCode, $store);
        if (!empty($carrierConfig)) {
            return $this->_getCarrier($carrierCode, $carrierConfig, $store);
        }
        return false;
    }

    /**
     * Get carrier model object
     *
     * @param string $code
     * @param array $config
     * @param mixed $store
     * @return \Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    protected function _getCarrier($code, $config, $store = null)
    {
        if (!isset($config['model'])) {
            return false;
        }
        $modelName = $config['model'];

        /**
         * Added protection from not existing models usage.
         * Related with module uninstall process
         */
        try {
            $carrier = \Mage::getModel($modelName);
        } catch (\Exception $e) {
            \Mage::logException($e);
            return false;
        }
        $carrier->setId($code)->setStore($store);
        self::$_carriers[$code] = $carrier;
        return self::$_carriers[$code];
    }
}
