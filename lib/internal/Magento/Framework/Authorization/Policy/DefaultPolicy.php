<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default authorization policy. Allows access to all resources
 */
namespace Magento\Framework\Authorization\Policy;

class DefaultPolicy implements \Magento\Framework\Authorization\Policy
{
    /**
     * Check whether given role has access to give id
     *
     * @param string $roleId
     * @param string $resourceId
     * @param string $privilege
     * @return true
     */
    public function isAllowed($roleId, $resourceId, $privilege = null)
    {
        return true;
    }
}
