<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Authorization
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default authorization policy. Allows access to all resources
 */
namespace Magento\Authorization\Policy;

class DefaultPolicy implements \Magento\Authorization\Policy
{
    /**
     * Check whether given role has access to give id
     *
     * @param string $roleId
     * @param string $resourceId
     * @param mixed $privilege
     * @return bool
     */
    public function isAllowed($roleId, $resourceId, $privilege = null)
    {
        return true;
    }
}
