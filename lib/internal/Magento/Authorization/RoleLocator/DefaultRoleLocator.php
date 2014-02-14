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

namespace Magento\Authorization\RoleLocator;

class DefaultRoleLocator implements \Magento\Authorization\RoleLocator
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
