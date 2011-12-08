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
 * Convert profile collection
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Profile_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define resource model and model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Dataflow_Model_Profile', 'Mage_Dataflow_Model_Resource_Profile');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Mage_Dataflow_Model_Resource_Profile_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()
            ->where('main_table.store_id IN (?)', array(0, $storeIds));
        return $this;
    }
}
