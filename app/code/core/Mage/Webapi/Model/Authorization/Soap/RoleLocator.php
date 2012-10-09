<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
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
     * Initialize username token.
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_usernameToken = isset($data['usernameToken']) ? $data['usernameToken'] : null;
    }

    /**
     * Retrieve current role.
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
