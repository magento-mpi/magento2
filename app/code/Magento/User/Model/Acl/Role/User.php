<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Acl\Role;

/**
 * User acl role
 */
class User extends \Magento\User\Model\Acl\Role\Generic
{
    /**
     * All the user roles are prepended by U
     *
     */
    const ROLE_TYPE = 'U';
}
