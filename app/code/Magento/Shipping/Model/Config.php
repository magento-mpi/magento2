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

    /**
     * Core store config
     *
     * @var \Magento\Store\Model\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Constructor
     *
     * @param \Magento\Store\Model\Config $coreStoreConfig
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\Config $coreStoreConfig,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
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
                $carrierModel = $this->_carrierFactory->create($carrierCode, $store);
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
            $model = $this->_carrierFactory->create($carrierCode, $store);
            if ($model) {
                $carriers[$carrierCode] = $model;
            }
        }
        return $carriers;
    }
}
