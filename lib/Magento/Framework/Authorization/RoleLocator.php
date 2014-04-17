<?php
/**
 * Links Authorization component with application.
 * Responsible for providing the identifier of currently logged in role to \Magento\Authorization component.
 * Should be implemented by application developer that uses \Magento\Authorization component.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization;

interface RoleLocator
{
    /**
     * Retrieve current role
     *
     * @return string
     */
    public function getAclRoleId();
}
