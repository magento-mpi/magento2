<?php
/**
 * Acl User factory
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Acl_User_Factory extends Mage_Oauth_Model_Consumer_Factory
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
     * Create ACL user model
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Acl_User
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Acl_User', $arguments);
    }
}
