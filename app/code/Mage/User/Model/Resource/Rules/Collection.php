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
 * Rules collection
 *
 * @category    Mage
 * @package     Mage_User
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Resource_Rules_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_User_Model_Rules', 'Mage_User_Model_Resource_Rules');
    }

    /**
     * Get rules by role id
     *
     * @param int $roleId
     * @return Mage_User_Model_Resource_Rules_Collection
     */
    public function getByRoles($roleId)
    {
        $this->addFieldToFilter('role_id', (int) $roleId);
        return $this;
    }

    /**
     * Sort by length
     *
     * @return Mage_User_Model_Resource_Rules_Collection
     */
    public function addSortByLength()
    {
        $length = $this->getConnection()->getLengthSql('{{resource_id}}');
        $this->addExpressionFieldToSelect('length', $length, 'resource_id');
        $this->getSelect()->order('length ' . Zend_Db_Select::SQL_DESC);

        return $this;
    }
}
