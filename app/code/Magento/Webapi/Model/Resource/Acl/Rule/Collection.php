<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Web API Rules Resource Collection.
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Webapi\Model\Resource\Acl\Rule;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization.
     */
    protected function _construct()
    {
        $this->_init('\Magento\Webapi\Model\Acl\Rule', '\Magento\Webapi\Model\Resource\Acl\Rule');
    }

    /**
     * Retrieve rules by role.
     *
     * @param int $roleId
     * @return \Magento\Webapi\Model\Resource\Acl\Rule\Collection
     */
    public function getByRole($roleId)
    {
        $this->getSelect()->where("role_id = ?", (int)$roleId);
        return $this;
    }
}
