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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Constructor
     *
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param array $data
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        array $data = array()
    ) {
        $this->_storeConfig = $coreStoreConfig;
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
        $config = $this->_storeConfig->getValue('carriers', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        foreach (array_keys($config) as $carrierCode) {
            if ($this->_storeConfig->isSetFlag('carriers/' . $carrierCode . '/active', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store)) {
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
        $config = $this->_storeConfig->getValue('carriers', \Magento\Core\Model\StoreManagerInterface::SCOPE_TYPE_STORE, $store);
        foreach (array_keys($config) as $carrierCode) {
            $model = $this->_carrierFactory->create($carrierCode, $store);
            if ($model) {
                $carriers[$carrierCode] = $model;
            }
        }
        return $carriers;
    }
}
