<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Model_CustomerFactory
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
     * Create customer model instance.
     *
     * @param array $arguments
     * @return Mage_Customer_Model_Customer
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Customer_Model_Customer', $arguments, false);
    }
}
