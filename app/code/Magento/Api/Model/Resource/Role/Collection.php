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
 * Api Role Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource\Role;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Api\Model\Role', 'Magento\Api\Model\Resource\Role');
    }

    /**
     * Aet user filter
     *
     * @param int $userId
     * @return \Magento\Api\Model\Resource\Role\Collection
     */
    public function setUserFilter($userId)
    {
        $this->addFieldToFilter('user_id', $userId);
        $this->addFieldToFilter('role_type', \Magento\Api\Model\Acl::ROLE_TYPE_GROUP);
        return $this;
    }

    /**
     * Set roles filter
     *
     * @return \Magento\Api\Model\Resource\Role\Collection
     */
    public function setRolesFilter()
    {
        $this->addFieldToFilter('role_type', \Magento\Api\Model\Acl::ROLE_TYPE_GROUP);
        return $this;
    }
}
