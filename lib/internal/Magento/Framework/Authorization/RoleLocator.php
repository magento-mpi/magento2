<?php
/**
 * Links Authorization component with application.
 * Responsible for providing the identifier of currently logged in role to \Magento\Framework\Authorization component.
 * Should be implemented by application developer that uses \Magento\Framework\Authorization component.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Authorization;

interface RoleLocator
{
    /**
     * Retrieve current role
     *
     * @return string|null
     */
    public function getAclRoleId();
}
