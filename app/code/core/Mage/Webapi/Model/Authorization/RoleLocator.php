<?php
/**
 * Api Acl RoleLocator
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Authorization_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * @var string|null
     */
    protected $_roleId = null;

    /**
     * Initialize role id
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_roleId = isset($data['roleId']) ? $data['roleId'] : null;
    }

    /**
     * Set role id into role locator
     *
     * @param string $roleId
     */
    public function setRoleId($roleId)
    {
        $this->_roleId = $roleId;
    }

    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId()
    {
        return $this->_roleId;
    }
}
