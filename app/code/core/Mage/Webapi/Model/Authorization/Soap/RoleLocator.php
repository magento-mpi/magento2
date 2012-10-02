<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Webapi_Model_Authorization_Soap_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * @var Mage_Webapi_Model_Soap_Security_UsernameToken
     */
    protected $_usernameToken;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_usernameToken = $data['usernameToken'];
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        if ($this->_usernameToken) {
            return $this->_usernameToken->authenticate()->getRoleId();
        }
        return null;
    }
}
