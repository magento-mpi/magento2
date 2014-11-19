<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Authorization\RoleLocator;

class DefaultRoleLocator implements \Magento\Framework\Authorization\RoleLocatorInterface
{
    /**
     * Retrieve current role
     *
     * @return string
     */
    public function getAclRoleId()
    {
        return '';
    }
}
