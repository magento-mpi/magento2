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
 * Api Roles Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Resource\Roles;

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
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('role_id', 'role_name');
    }

    /**
     * Init collection select
     *
     * @return \Magento\Api\Model\Resource\Roles\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->where('main_table.role_type = ?', \Magento\Api\Model\Acl::ROLE_TYPE_GROUP);
        return $this;
    }
}
