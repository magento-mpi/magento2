<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Plugin;

/**
 * Plugin around \Magento\Framework\Authorization::isAllowed to allow guest users to access resources with
 * anonymous permission.
 */
class GuestAuthorization
{
    /**
     * Check if resource for which access is needed has anonymous permissions defined in webapi config.
     *
     * @param \Magento\Framework\Authorization $subject
     * @param callable $proceed
     * @param string $resource
     * @param string $privilege
     *
     * @return bool true If resource permission is anonymous,
     * to allow any user access without further checks in parent method
     */
    public function aroundIsAllowed(
        \Magento\Framework\Authorization $subject,
        \Closure $proceed,
        $resource,
        $privilege = null
    ) {
        if ($resource == 'anonymous') {
            return true;
        } else {
            return $proceed($resource, $privilege);
        }
    }
}
