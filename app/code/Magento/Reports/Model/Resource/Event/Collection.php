<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report event collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Resource_Event_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Store Ids
     *
     * @var array
     */
    protected $_storeIds;

    /**
     * Resource initializations
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Reports_Model_Event', 'Magento_Reports_Model_Resource_Event');
    }

    /**
     * Add store ids filter
     *
     * @param array $storeIds
     * @return Magento_Reports_Model_Resource_Event_Collection
     */
    public function addStoreFilter(array $storeIds)
    {
        $this->_storeIds = $storeIds;
        return $this;
    }

    /**
     * Add recently filter
     *
     * @param int $typeId
     * @param int $subjectId
     * @param int $subtype
     * @param int|array $ignore
     * @param int $limit
     * @return Magento_Reports_Model_Resource_Event_Collection
     */
    public function addRecentlyFiler($typeId, $subjectId, $subtype = 0, $ignore = null, $limit = 15)
    {
        $stores = $this->getResource()->getCurrentStoreIds($this->_storeIds);
        $select = $this->getSelect();
        $select->where('event_type_id = ?', $typeId)
            ->where('subject_id = ?', $subjectId)
            ->where('subtype = ?', $subtype)
            ->where('store_id IN(?)', $stores);
        if ($ignore) {
            if (is_array($ignore)) {
                $select->where('object_id NOT IN(?)', $ignore);
            } else {
                $select->where('object_id <> ?', $ignore);
            }
        }
        $select->group('object_id')
            ->limit($limit);
        return $this;
    }
}
