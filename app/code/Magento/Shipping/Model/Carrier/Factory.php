<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Shipping_Model_Carrier_Factory
{
    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Logger $logger,
        Magento_ObjectManager $objectManager
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $carrierCode
     * @param int|null $storeId
     * @return bool|Magento_Shipping_Model_Carrier_Abstract
     */
    public function create($carrierCode, $storeId = null)
    {
        $className = $this->_coreStoreConfig->getConfig('carriers/' . $carrierCode . '/model', $storeId);
        if (!$className) {
            return false;
        }

        /**
         * Added protection from not existing models usage.
         * Related with module uninstall process
         */
        try {
            $carrier = $this->_objectManager->create($className);
        } catch (Exception $e) {
            $this->_logger->logException($e);
            return false;
        }

        $carrier->setId($carrierCode);
        if ($storeId) {
            $carrier->setStore($storeId);
        }
        return $carrier;
    }
}
