<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 Global ACL Rule model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule_Collection getResourceCollection()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule getResource()
 * @method Mage_Api2_Model_Resource_Acl_Global_Rule _getResource()
 * @method int getRoleId()
 * @method Mage_Api2_Model_Acl_Global_Rule setRoleId() setRoleId(int $roleId)
 * @method string getResourceId()
 * @method Mage_Api2_Model_Acl_Global_Rule setResourceId() setResourceId(string $resource)
 * @method string getPrivilege()
 * @method int getPermission()
 * @method Mage_Api2_Model_Acl_Global_Rule setPermission() setPermission(int $permission)
 * @method string getPrivilege()
 * @method Mage_Api2_Model_Acl_Global_Rule setPrivilege() setPrivilege(string $privilege)
 * @method string getAllowedAttributes()
 * @method Mage_Api2_Model_Acl_Global_Rule setAllowedAttributes() setAllowedAttributes(string $allowedAttributes)
 */
class Mage_Api2_Model_Acl_Global_Rule extends Mage_Core_Model_Abstract
{
    /**
     * Root resource ID "all"
     */
    const RESOURCE_ALL = 'all';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Api2_Model_Acl_Global_Rule');
    }
}
