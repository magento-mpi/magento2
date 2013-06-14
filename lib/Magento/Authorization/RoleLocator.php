<?php
/**
 * Links Authorization component with application.
 * Responsible for providing the identifier of currently logged in role to Magento_Authorization component.
 * Should be implemented by application developer that uses Magento_Authorization component.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Authorization_RoleLocator
{
    /**
     * Retrieve current role
     *
     * @return string
     */
    public function getAclRoleId();
}
