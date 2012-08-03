<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Authorization_RoleLocator implements Magento_Authorization_RoleLocator
{
    /**
     * Retrieve current role
     *
     * @return string
     */
    public function getAclRoleId()
    {
        return 'U1';
    }
}
