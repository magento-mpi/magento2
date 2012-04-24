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
 * Admin role users collection
 *
 * @category    Mage
 * @package     Mage_Admin
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Admin_Model_Resource_Roles_User_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Admin_Model_User', 'Mage_Admin_Model_Resource_User');
    }

    /**
     * Initialize select
     *
     * @return Mage_Admin_Model_Resource_Roles_User_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->where("user_id > 0");

        return $this;
    }
}
