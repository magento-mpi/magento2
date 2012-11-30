<?php
/**
 * Customer address factory
 *
 * @copyright {}
 */
class Mage_Customer_Model_Address_Factory
{
    const ADDRESS_CLASS_NAME = 'Mage_Customer_Model_Address';

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
        return $this->_objectManager->create(self::ADDRESS_CLASS_NAME, $arguments, false);
    }
}
