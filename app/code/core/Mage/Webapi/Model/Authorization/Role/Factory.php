<?php
/**
 * Factory for Mage_Webapi_Model_Authorization_Role
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_Role_Factory
{
    const ROLE_CLASS_NAME = 'Mage_Webapi_Model_Authorization_Role';

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
     * Return new ACL role model
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Authorization_Role
     */
    public function createRole(array $arguments = array())
    {
        return $this->_objectManager->create(self::ROLE_CLASS_NAME, $arguments, false);
    }
}
