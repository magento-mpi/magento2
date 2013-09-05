<?php
/**
 * Factory for Magento_Webapi_Model_Authorization_RoleLocator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Role_Locator_Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of Magento_Webapi_Model_Authorization_RoleLocator
     *
     * @param array $arguments fed into constructor
     * @return Magento_Webapi_Model_Authorization_RoleLocator
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Webapi_Model_Authorization_RoleLocator', $arguments);
    }
}
