<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_ContainerFactory
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
     * Get config data container instance
     *
     * @param array $arguments
     * @return Mage_Core_Model_ConfigInterface
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Mage_Core_Model_Config_Container', $arguments);
    }
}
