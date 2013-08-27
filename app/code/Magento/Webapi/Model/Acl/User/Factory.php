<?php
/**
 * ACL User factory.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_User_Factory extends Magento_Oauth_Model_Consumer_Factory
{
    const CLASS_NAME = 'Magento_Webapi_Model_Acl_User';

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
     * Create ACL user model.
     *
     * @param array $arguments
     * @return Magento_Webapi_Model_Acl_User
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments);
    }
}
