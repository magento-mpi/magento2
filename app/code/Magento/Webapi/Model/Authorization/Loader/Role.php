<?php
/**
 * API ACL Role Loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Authorization\Loader;

class Role implements \Magento\Acl\LoaderInterface
{
    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Role
     */
    protected $_roleResource;

    /**
     * @var \Magento\Webapi\Model\Authorization\Role\Factory
     */
    protected $_roleFactory;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Webapi\Model\Resource\Acl\Role $roleResource
     * @param \Magento\Webapi\Model\Authorization\Role\Factory $roleFactory
     */
    public function __construct(\Magento\Webapi\Model\Resource\Acl\Role $roleResource,
        \Magento\Webapi\Model\Authorization\Role\Factory $roleFactory
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
            /** @var $aclRole \Magento\Webapi\Model\Authorization\Role */
            $aclRole = $this->_roleFactory->createRole(array('roleId' => $roleId));
            $acl->addRole($aclRole);
            //Deny all privileges to Role. Some of them could be allowed later by whitelist
            $acl->deny($aclRole);
        }
    }
}
