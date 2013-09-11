<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Role Model
 *
 * @method \Magento\User\Model\Resource\Role _getResource()
 * @method \Magento\User\Model\Resource\Role getResource()
 * @method int getParentId()
 * @method \Magento\User\Model\Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method \Magento\User\Model\Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\User\Model\Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method \Magento\User\Model\Role setRoleType(string $value)
 * @method int getUserId()
 * @method \Magento\User\Model\Role setUserId(int $value)
 * @method string getRoleName()
 * @method \Magento\User\Model\Role setRoleName(string $value)
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model;

class Role extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'admin_roles';

    protected function _construct()
    {
        $this->_init('\Magento\User\Model\Resource\Role');
    }

    /**
     * Update object into database
     *
     * @return \Magento\User\Model\Role
     */
    public function update()
    {
        $this->getResource()->update($this);
        return $this;
    }

    /**
     * Retrieve users collection
     *
     * @return \Magento\User\Model\Resource\Role\User\Collection
     */
    public function getUsersCollection()
    {
        return \Mage::getResourceModel('\Magento\User\Model\Resource\Role\User\Collection');
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
