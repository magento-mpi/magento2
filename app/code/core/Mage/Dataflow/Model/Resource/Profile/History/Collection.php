<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Convert history collection
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Profile_History_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Profile_History', 'Mage_Dataflow_Model_Resource_Profile_History');
    }

    /**
     * Joins admin data to select
     *
     * @return Mage_Dataflow_Model_Resource_Profile_History_Collection
     */
    public function joinAdminUser()
    {
        $this->getSelect()->join(
            array('u' => $this->getTable('admin_user')),
            'u.user_id=main_table.user_id',
            array('firstname', 'lastname')
        );
        return $this;
    }
}
