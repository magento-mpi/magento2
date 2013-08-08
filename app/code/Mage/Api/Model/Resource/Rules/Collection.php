<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Api Rules Resource Collection
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_Rules_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource collection initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Api_Model_Rules', 'Mage_Api_Model_Resource_Rules');
    }

    /**
     * Retrieve rules by role
     *
     * @param int $id
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function getByRoles($id)
    {
        $this->getSelect()->where("role_id = ?", (int)$id);
        return $this;
    }

    /**
     * Add sort by length
     *
     * @return Mage_Api_Model_Resource_Rules_Collection
     */
    public function addSortByLength()
    {
        $this->getSelect()->columns(array('length' => $this->getConnection()->getLengthSql('resource_id')))
            ->order('length DESC');
        return $this;
    }
}
