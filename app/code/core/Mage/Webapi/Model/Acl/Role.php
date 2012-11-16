<?php
/**
 * Role item model
 *
 * @copyright {}
 *
 * @method int getRoleId()
 * @method string getRoleName()
 * @method Mage_Webapi_Model_Acl_Role setRoleName(string $value)
 */
class Mage_Webapi_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Mage_Webapi_Model_Resource_Acl_Role');
    }
}
