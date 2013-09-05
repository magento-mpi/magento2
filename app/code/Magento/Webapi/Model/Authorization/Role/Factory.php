<?php
/**
 * Factory for Magento_Webapi_Model_Authorization_Role
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Role_Factory
{
    const ROLE_CLASS_NAME = 'Magento_Webapi_Model_Authorization_Role';

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
     * Return new ACL role model.
     *
     * @param array $arguments
     * @return Magento_Webapi_Model_Authorization_Role
     */
    public function createRole(array $arguments = array())
    {
        return $this->_objectManager->create(self::ROLE_CLASS_NAME, $arguments);
    }
}
