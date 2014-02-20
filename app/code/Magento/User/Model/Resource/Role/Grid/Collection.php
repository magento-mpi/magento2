<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\Role\Grid;

use Magento\User\Model\Acl\Role\Group as RoleGroup;

/**
 * Admin role data grid collection
 */
class Collection extends \Magento\User\Model\Resource\Role\Collection
{
    /**
     * Prepare select for load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('role_type', RoleGroup::ROLE_TYPE);
        return $this;
    }
}
