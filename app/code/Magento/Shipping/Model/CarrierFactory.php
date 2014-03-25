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
     * @var \Magento\App\Config\ScopeConfigInterface
     */
    protected $_storeConfig;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\App\Config\ScopeConfigInterface $coreStoreConfig
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\App\Config\ScopeConfigInterface $coreStoreConfig,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_storeConfig = $coreStoreConfig;
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
        $className = $this->_storeConfig->getValue(
            'carriers/' . $carrierCode . '/model',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
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
        $className = $this->_storeConfig->getValue(
            'carriers/' . $carrierCode . '/model',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
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
        return $this->_storeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ? $this->get(
            $carrierCode
        ) : false;
    }

    /**
     * Create carrier by its code if it is active
     *
     * @param string $carrierCode
     * @param null|int $storeId
     * @return bool|Carrier\AbstractCarrier
     */
    public function createIfActive($carrierCode, $storeId = null)
    {
        return $this->_storeConfig->isSetFlag(
            'carriers/' . $carrierCode . '/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) ? $this->create(
            $carrierCode,
            $storeId
        ) : false;
    }
}
