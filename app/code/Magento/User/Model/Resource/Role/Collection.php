<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\Role;

use Magento\User\Model\Acl\Role\Group as RoleGroup;

/**
 * Admin role collection
 */
class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\Role', 'Magento\User\Model\Resource\Role');
    }

    /**
     * Add user filter
     *
     * @param int $userId
     * @param string $userType
     * @return $this
     */
    public function setUserFilter($userId, $userType)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('user_type', $userType);
        return $this;
    }

    /**
     * Add user type filter
     *
     * @param string $userType
     * @return $this
     */
    public function setUserTypeFilter($userType)
    {
        $this->addFieldToFilter('user_type', $userType);
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return $this
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', RoleGroup::ROLE_TYPE);
        return $this;
    }

    /**
     * Convert to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('role_id', 'role_name');
    }
}
