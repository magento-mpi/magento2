<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Config_ContainerFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get config data container instance
     *
     * @param array $arguments
     * @return Magento_Core_Model_ConfigInterface
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Core_Model_Config_Container', $arguments);
    }
}
