<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * User acl role
 *
 * @method Mage_Api_Model_Resource_Role _getResource()
 * @method Mage_Api_Model_Resource_Role getResource()
 * @method int getParentId()
 * @method Mage_Api_Model_Acl_Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method Mage_Api_Model_Acl_Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method Mage_Api_Model_Acl_Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method Mage_Api_Model_Acl_Role setRoleType(string $value)
 * @method int getUserId()
 * @method Mage_Api_Model_Acl_Role setUserId(int $value)
 * @method string getRoleName()
 * @method Mage_Api_Model_Acl_Role setRoleName(string $value)
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Acl_Role extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Api_Model_Resource_Role');
    }
}
