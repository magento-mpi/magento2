<?php
/**
 * Responsible for internal authorization decision making based on provided role, resource and privilege
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorization;

interface Policy
{
    /**
     * Check whether given role has access to given resource
     *
     * @abstract
     * @param string $roleId
     * @param string $resourceId
     * @param mixed $privilege
     * @return bool
     */
    public function isAllowed($roleId, $resourceId, $privilege = null);
}
