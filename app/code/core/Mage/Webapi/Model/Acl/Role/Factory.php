<?php
/**
 * Acl Role factory
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Acl_Role_Factory
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
     * Create ACL role model
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Acl_Role
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Acl_Role', $arguments);
    }
}
