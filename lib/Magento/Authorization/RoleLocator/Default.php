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

class Magento_Authorization_RoleLocator_Default implements Magento_Authorization_RoleLocator
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
