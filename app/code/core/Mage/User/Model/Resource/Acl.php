<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource model for admin ACL
 */
class Mage_User_Model_Resource_Acl extends Mage_Core_Model_Resource_Db_Abstract
{
    const ACL_ALL_RULES = 'all';

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('admin_role', 'role_id');
    }

    /**
     * Load ACL for the user
     *
     * @return Magento_Acl
     */
    public function loadAcl()
    {

        return $acl;
    }
}
