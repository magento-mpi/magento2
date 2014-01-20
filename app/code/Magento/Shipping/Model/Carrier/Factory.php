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
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Logger $logger
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Logger $logger,
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

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return bool|mixed
     */
    public function getByOrder(\Magento\Sales\Model\Order $order)
    {
        $method = $order->getShippingMethod(true);
        $carrierModel = $this->create($method->getCarrierCode());

        return $carrierModel;
    }

    /**
     * Get carrier by its code
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|\Magento\Core\Model\AbstractModel
     */
    public function getIfActive($carrierCode, $storeId = null)
    {
        $isActive = $this->_coreStoreConfig
            ->getConfigFlag('carriers/' . $carrierCode . '/active', $storeId);
        if (!$isActive) {
            return false;
        }

        return $this->create($carrierCode, $storeId);
    }
}
