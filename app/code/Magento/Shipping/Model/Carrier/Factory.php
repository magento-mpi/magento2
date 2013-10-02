<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Model\Carrier;

class Factory
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\Logger $logger,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_logger = $logger;
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $carrierCode
     * @param int|null $storeId
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrier
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
        } catch (\Exception $e) {
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
