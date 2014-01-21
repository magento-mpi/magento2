<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model;

use Magento\Sales\Model\Quote\Address\CarrierFactoryInterface;

class CarrierFactory implements CarrierFactoryInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get carrier instance
     *
     * @param string $carrierCode
     * @return bool|Carrier\AbstractCarrier
     */
    public function get($carrierCode)
    {
        $className = $this->_coreStoreConfig->getConfig('carriers/' . $carrierCode . '/model');
        if (!$className) {
            return false;
        }
        $carrier = $this->_objectManager->get($className);
        $carrier->setId($carrierCode);
        return $carrier;
    }

    /**
     * Create carrier instance
     *
     * @param string $carrierCode
     * @param int|null $storeId
     * @return bool|Carrier\AbstractCarrier
     */
    public function create($carrierCode, $storeId = null)
    {
        $className = $this->_coreStoreConfig->getConfig('carriers/' . $carrierCode . '/model', $storeId);
        if (!$className) {
            return false;
        }
        $carrier = $this->_objectManager->create($className);
        $carrier->setId($carrierCode);
        if ($storeId) {
            $carrier->setStore($storeId);
        }
        return $carrier;
    }

    /**
     * Get carrier by its code if it is active
     *
     * @param string $carrierCode
     * @return bool|Carrier\AbstractCarrier
     */
    public function getIfActive($carrierCode)
    {
        return $this->_coreStoreConfig->getConfigFlag('carriers/' . $carrierCode . '/active')
            ? $this->get($carrierCode)
            : false;
    }

    /**
     * Create carrier by its code if it is active
     *
     * @param $carrierCode
     * @param null|int $storeId
     * @return bool|Carrier\AbstractCarrier
     */
    public function createIfActive($carrierCode, $storeId = null)
    {
        return $this->_coreStoreConfig->getConfigFlag('carriers/' . $carrierCode . '/active')
            ? $this->create($carrierCode, $storeId)
            : false;
    }
}
