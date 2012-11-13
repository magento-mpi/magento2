<?php
/**
 * Customer address factory
 *
 * @copyright {}
 */
class Mage_Customer_Model_AddressFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create customer address model instance.
     *
     * @param array $arguments
     * @return Mage_Customer_Model_Address
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Customer_Model_Address', $arguments, false);
    }
}
