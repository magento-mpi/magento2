<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pool resource collection model
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Poll_Model_Resource_Poll_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection
     *
     */
    public function _construct()
    {
        $this->_init('Magento_Poll_Model_Poll', 'Magento_Poll_Model_Resource_Poll');
    }

    /**
     * Redefine default filters
     *
     * @param string $field
     * @param mixed $condition
     * @return Magento_Data_Collection_Db
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'stores') {
            return $this->addStoreFilter($condition);
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Add Stores Filter
     *
     * @param mixed $storeId
     * @param bool  $withAdmin
     * @return Magento_Poll_Model_Resource_Poll_Collection
     */
    public function addStoreFilter($storeId, $withAdmin = true)
    {
        $this->getSelect()->join(
            array('store_table' => $this->getTable('poll_store')),
            'main_table.poll_id = store_table.poll_id',
            array()
        )
        ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $storeId) : $storeId))
        ->group('main_table.poll_id');
        return $this;
    }

    /**
     * Add stores data
     *
     * @return Magento_Poll_Model_Resource_Poll_Collection
     */
    public function addStoreData()
    {
        $pollIds = $this->getColumnValues('poll_id');
        $storesToPoll = array();

        if (count($pollIds) > 0) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('poll_store'))
                ->where('poll_id IN(?)', $pollIds);
            $result = $this->getConnection()->fetchAll($select);

            foreach ($result as $row) {
                if (!isset($storesToPoll[$row['poll_id']])) {
                    $storesToPoll[$row['poll_id']] = array();
                }
                $storesToPoll[$row['poll_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToPoll[$item->getId()])) {
                $item->setStores($storesToPoll[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }

    /**
     * Set stores of the current poll
     *
     * @return Magento_Poll_Model_Resource_Poll_Collection
     */
    public function addSelectStores()
    {
        $pollId = $this->getId();
        $select = $this->getConnection()->select()
            ->from($this->getTable('poll_store'), array('stor_id'))
            ->where('poll_id = :poll_id');
        $stores = $this->getConnection()->fetchCol($select, array(':poll_id' => $pollId));
        $this->setSelectStores($stores);

        return $this;
    }
}
