<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authz\Service;

use Magento\Authz\Model\UserContextInterface;

/**
 * Authorization service interface.
 */
interface AuthorizationV1Interface
{
    /**
     * Grant permissions to user to access the specified resources.
     *
     * @param UserContextInterface $userContext
     * @param string[] $resources List of resources which should be available to the specified user.
     */
    public function grandPermission($userContext, $resources);

    /**
     * Check if the user has permission to access the requested resource.
     *
     * @param UserContextInterface $userContext
     * @param string $resource
     * @return bool
     */
    public function isAllowed($userContext, $resource);

    /**
     * Retrieve all the roles in the system.
     *
     * @return \Zend_Acl_Role_Interface[]
     */
    public function getRoles();

    /**
     * Retrieve role by given role id.
     *
     * @param string $roleId
     * @return \Zend_Acl_Role_Interface
     */
    public function getRole($roleId);

    /**
     * Create new role with the access to the given set of resources.
     *
     * @param string[] $resources
     * @return \Zend_Acl_Role_Interface
     */
    public function createRole($resources);
}
