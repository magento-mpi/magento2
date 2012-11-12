<?php
/**
 * User builder factory.
 *
 * @copyright {copyright}
 */
class Mage_Webapi_Model_Acl_UserFactory
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
     * Create user model.
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Acl_User
     */
    public function createFromArray($arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Acl_User', $arguments);
    }
}
