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
 * Admin role collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\User\Model\Resource\Role;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     *
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
     * @return \Magento\User\Model\Resource\Role\Collection
     */
    public function setUserFilter($userId, $userType)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('user_type', $userType);
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return \Magento\User\Model\Resource\Role\Collection
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', \Magento\User\Model\Acl\Role\Group::ROLE_TYPE);
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
