<?php
/**
 * API ACL Role Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authorization_Loader_Role implements \Magento\Acl\LoaderInterface
{
    /**
     * @var Magento_Webapi_Model_Resource_Acl_Role
     */
    protected $_roleResource;

    /**
     * @var Magento_Webapi_Model_Authorization_Role_Factory
     */
    protected $_roleFactory;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Webapi_Model_Resource_Acl_Role $roleResource
     * @param Magento_Webapi_Model_Authorization_Role_Factory $roleFactory
     */
    public function __construct(Magento_Webapi_Model_Resource_Acl_Role $roleResource,
        Magento_Webapi_Model_Authorization_Role_Factory $roleFactory
    ) {
        $this->_roleResource = $roleResource;
        $this->_roleFactory = $roleFactory;
    }

    /**
     * Populate ACL with roles from external storage.
     *
     * @param \Magento\Acl $acl
     */
    public function populateAcl(\Magento\Acl $acl)
    {
        $roleList = $this->_roleResource->getRolesIds();
        foreach ($roleList as $roleId) {
            /** @var $aclRole Magento_Webapi_Model_Authorization_Role */
            $aclRole = $this->_roleFactory->createRole(array('roleId' => $roleId));
            $acl->addRole($aclRole);
            //Deny all privileges to Role. Some of them could be allowed later by whitelist
            $acl->deny($aclRole);
        }
    }
}
