<?php
/**
 * Responsible for internal authorization decision making based on provided role, resource and privilege
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Authorization;

interface PolicyInterface
{
    /**
     * Check whether given role has access to given resource
     *
     * @abstract
     * @param string $roleId
     * @param string $resourceId
     * @param string|null $privilege
     * @return bool
     */
    public function isAllowed($roleId, $resourceId, $privilege = null);
}
