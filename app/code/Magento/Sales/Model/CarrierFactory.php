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
 * Factory class for \Magento\Core\Model\AbstractModel
 */
namespace Magento\Sales\Model;

class CarrierFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Factory constructor
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $carrierCode
     * @param array $data
     * @return \Magento\Core\Model\AbstractModel|bool
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
