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
 * Admin Role Model
 *
 * @method Mage_User_Model_Resource_Role _getResource()
 * @method Mage_User_Model_Resource_Role getResource()
 * @method int getParentId()
 * @method Mage_User_Model_Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method Mage_User_Model_Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method Mage_User_Model_Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method Mage_User_Model_Role setRoleType(string $value)
 * @method int getUserId()
 * @method Mage_User_Model_Role setUserId(int $value)
 * @method string getRoleName()
 * @method Mage_User_Model_Role setRoleName(string $value)
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Role extends Magento_Core_Model_Abstract
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'admin_roles';

    protected function _construct()
    {
        $this->_init('Mage_User_Model_Resource_Role');
    }

    /**
     * Update object into database
     *
     * @return Mage_User_Model_Role
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Retrieve users collection
     *
     * @return Mage_User_Model_Resource_Role_User_Collection
     */
    public function getUsersCollection()
    {
        return Mage::getResourceModel('Mage_User_Model_Resource_Role_User_Collection');
    }

    /**
     * Return users for role
     *
     * @return array
     */
    public function getRoleUsers()
    {
        return $this->getResource()->getRoleUsers($this);
    }
}
