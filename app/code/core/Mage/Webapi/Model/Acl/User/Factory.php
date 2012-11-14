<?php
/**
 * User builder factory.
 *
 * @copyright {copyright}
 */
class Mage_Webapi_Model_Acl_User_Factory extends Mage_Oauth_Model_Consumer_Factory
{
    /**
     * Create user model.
     *
     * @param array $arguments
     * @return Mage_Webapi_Model_Acl_User
     */
    public function create($arguments = array())
    {
        return $this->_objectManager->create('Mage_Webapi_Model_Acl_User', $arguments);
    }
}
