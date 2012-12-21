<?php
/**
 * Customer factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Customer_Model_Customer_Factory
{
    const CUSTOMER_CLASS_NAME = 'Mage_Customer_Model_Customer';

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
     * Create customer model instance.
     *
     * @param array $arguments
     * @return Mage_Customer_Model_Customer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CUSTOMER_CLASS_NAME, $arguments, false);
    }
}
