<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for Magento_Core_Model_Abstract
 */
class Magento_Sales_Model_CarrierFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Factory constructor
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $carrierCode
     * @param array $data
     * @return Magento_Core_Model_Abstract|bool
     */
    public function create($carrierCode, array $data = array())
    {
        $className = $this->_storeManager->getStore()->getConfig('carriers/' . $carrierCode . '/model');
        if ($className) {
            return $this->_objectManager->create($className, $data);
        }
        return false;
    }
}
