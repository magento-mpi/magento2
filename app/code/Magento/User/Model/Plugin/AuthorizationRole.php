<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Model\Plugin;

use Magento\Authorization\Model\Role;

/**
 * Plugin for authorization role model
 */
class AuthorizationRole
{
    /** @var \Magento\User\Model\Resource\User */
    protected $userResourceModel;

    /**
     * Initialize dependencies
     */
    public function __construct(\Magento\User\Model\Resource\User $userResourceModel)
    {
        $this->userResourceModel = $userResourceModel;
    }

    /**
     * Update role users ACL.
     *
     * @param Role $subject
     * @param Role $result
     * @return Role
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Role $subject, Role $result)
    {
        $this->userResourceModel->updateRoleUsersAcl($subject);
        return $result;
    }
}
