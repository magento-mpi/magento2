<?php
/**
 * Users in role grid "In Role User" column with checkbox updater.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_Role_InRoleUserUpdater implements Magento_Core_Model_Layout_Argument_UpdaterInterface
{
    /**
     * @var int
     */
    protected $_roleId;

    /**
     * @var Magento_Webapi_Model_Resource_Acl_User
     */
    protected $_userResource;

    /**
     * Constructor.
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Webapi_Model_Resource_Acl_User $userResource
     */
    public function __construct(
        Magento_Core_Controller_Request_Http $request,
        Magento_Webapi_Model_Resource_Acl_User $userResource
    ) {
        $this->_roleId = (int)$request->getParam('role_id');
        $this->_userResource = $userResource;
    }

    /**
     * Init values with users assigned to role.
     *
     * @param array|null $values
     * @return array|null
     */
    public function update($values)
    {
        if ($this->_roleId) {
            $values = $this->_userResource->getRoleUsers($this->_roleId);
        }
        return $values;
    }
}
