<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\User\Model\Resource\Rules;

/**
 * Rules collection
 *
 * @category    Magento
 * @package     Magento_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\User\Model\Rules', 'Magento\User\Model\Resource\Rules');
    }

    /**
     * Get rules by role id
     *
     * @param int $roleId
     * @return $this
     */
    public function getByRoles($roleId)
    {
        $this->addFieldToFilter('role_id', (int) $roleId);
        return $this;
    }

    /**
     * Sort by length
     *
     * @return $this
     */
    public function addSortByLength()
    {
        $length = $this->getConnection()->getLengthSql('{{resource_id}}');
        $this->addExpressionFieldToSelect('length', $length, 'resource_id');
        $this->getSelect()->order('length ' . \Zend_Db_Select::SQL_DESC);

        return $this;
    }
}
