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
 * Api Rules Resource Collection
 *
 * @category    Magento
 * @package     Magento_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Api_Model_Resource_Rules_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Api_Model_Rules', 'Magento_Api_Model_Resource_Rules');
    }

    /**
     * Retrieve rules by role
     *
     * @param int $id
     * @return Magento_Api_Model_Resource_Rules_Collection
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where("role_id = ?", (int)$id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return Magento_Api_Model_Resource_Rules_Collection
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(array('length' => $this->getConnection()->getLengthSql('resource_id')))
            ->order('length DESC');
        return $this;
    }
}
