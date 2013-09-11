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
 * @method \Magento\Api\Model\Resource\Role _getResource()
 * @method \Magento\Api\Model\Resource\Role getResource()
 * @method int getParentId()
 * @method \Magento\Api\Model\Role setParentId(int $value)
 * @method int getTreeLevel()
 * @method \Magento\Api\Model\Role setTreeLevel(int $value)
 * @method int getSortOrder()
 * @method \Magento\Api\Model\Role setSortOrder(int $value)
 * @method string getRoleType()
 * @method \Magento\Api\Model\Role setRoleType(string $value)
 * @method int getUserId()
 * @method \Magento\Api\Model\Role setUserId(int $value)
 * @method string getRoleName()
 * @method \Magento\Api\Model\Role setRoleName(string $value)
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model;

class Role extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('\Magento\Api\Model\Resource\Role');
    }
}
