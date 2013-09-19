<?php
/**
 * Factory class for \Magento_Sales_Model_Quote_Address_Total_Abstract
 */
class Magento_Sales_Model_Quote_Address_TotalFactory
{
    /**
     * Object Manager instance
     *
     * @var \Magento_ObjectManager
     */
    protected $_objectManager = null;

    /**
     * Factory constructor
     *
     * @param \Magento_ObjectManager $objectManager
     */
    public function __construct(\Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param string $instanceName
     * @param array $data
     * @return \Magento_Sales_Model_Quote_Address_Total_Abstract
     */
    public function create($instanceName, array $data = array())
    {
        return $this->_objectManager->create($instanceName, $data);
    }
}
