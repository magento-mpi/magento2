<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Design resource collection
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Core_Model_Resource_Design_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Core Design resource collection
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Core_Model_Design', 'Magento_Core_Model_Resource_Design');
    }

    /**
     * Join store data to collection
     *
     * @return Magento_Core_Model_Resource_Design_Collection
     */
    public function joinStore()
    {
         return $this->join(
            array('cs' => 'core_store'),
            'cs.store_id = main_table.store_id',
            array('cs.name'));
    }

    /**
     * Add date filter to collection
     *
     * @param null|int|string|Zend_Date $date
     * @return Magento_Core_Model_Resource_Design_Collection
     */
    public function addDateFilter($date = null)
    {
        if (is_null($date)) {
            $date = $this->formatDate(true);
        } else {
            $date = $this->formatDate($date);
        }

        $this->addFieldToFilter('date_from', array('lteq' => $date));
        $this->addFieldToFilter('date_to', array('gteq' => $date));
        return $this;
    }

    /**
     * Add store filter to collection
     *
     * @param int|array $storeId
     * @return Magento_Core_Model_Resource_Design_Collection
     */
    public function addStoreFilter($storeId)
    {
        return $this->addFieldToFilter('store_id', array('in' => $storeId));
    }
}
