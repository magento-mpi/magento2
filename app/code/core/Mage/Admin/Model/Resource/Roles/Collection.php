<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Admin
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin roles collection
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Resource_Roles_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Admin_Model_Role', 'Mage_Admin_Model_Resource_Role');
    }

    /**
     * Init select
     *
     * @return Mage_Admin_Model_Resource_Roles_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where("main_table.role_type = ?", 'G');

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
