<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Role item model
 *
 * @method Magento_Api_Model_Resource_Role _getResource()
 * @method Magento_Api_Model_Resource_Role getResource()
 * @method int getParentId()
 * @method Magento_Api_Model_Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method Magento_Api_Model_Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method Magento_Api_Model_Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method Magento_Api_Model_Role setRoleType(string $value)
 * @method int getUserId()
 * @method Magento_Api_Model_Role setUserId(int $value)
 * @method string getRoleName()
 * @method Magento_Api_Model_Role setRoleName(string $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Role extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Resource_Role');
    }
}
