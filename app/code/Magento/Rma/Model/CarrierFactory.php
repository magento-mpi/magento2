<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory class for Magento_Usa_Model_Shipping_Carrier_Abstract
 */
class Magento_Rma_Model_CarrierFactory
{
    /**
     * Object Manager instance
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $className
     * @param array $data
     * @return Magento_Usa_Model_Shipping_Carrier_Abstract
     * @throws InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $carrier =  $this->_objectManager->create($className, $data);
        if ($carrier instanceof Magento_Usa_Model_Shipping_Carrier_Abstract) {
            throw new InvalidArgumentException("{$className} don't extend Magento_Usa_Model_Shipping_Carrier_Abstract");
        }
        return $carrier;
    }
}
