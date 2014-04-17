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
namespace Magento\Framework\Authorization\RoleLocator;

class DefaultRoleLocator implements \Magento\Framework\Authorization\RoleLocator
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
