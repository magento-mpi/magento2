<?php
/**
 * Event resource Collection
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Event_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Number of events to load at once;
     */
    const PAGE_SIZE = 100;

    public function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Webhook_Model_Event', 'Mage_Webhook_Model_Resource_Event');
    }

    /**
     * Adds FOR UPDATE lock on retrieved rows and filter status
     *
     * @return Mage_Webhook_Model_Resource_Event_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->forUpdate(true);
        $this->addFieldToFilter('status', Magento_PubSub_EventInterface::READY_TO_SEND)
            ->setOrder('created_at', Magento_Data_Collection::SORT_ORDER_ASC)
            ->setPageSize(self::PAGE_SIZE);
        return $this;
    }

    /**
     * Start transaction before executing the query in order to update the status atomically
     *
     * @return Mage_Webhook_Model_Resource_Event_Collection
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->getConnection()->beginTransaction();
        return $this;
    }

    /**
     * Update the status and commit transaction in case of success
     *
     * @return Mage_Webhook_Model_Resource_Event_Collection
     * @throws Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        try {
            $loadedIds = $this->_getLoadedIds();
            if (!empty($loadedIds)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => Magento_PubSub_EventInterface::IN_PROGRESS),
                    array('event_id IN (?)' => $loadedIds));
            }
            $this->getConnection()->commit();
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }

        return $this;
    }

    /**
     * Retrieve ids of all loaded records
     *
     * @return array
     */
    protected function _getLoadedIds()
    {
        $result = array();
        foreach ($this->getItems() as $item) {
            $result[] = $item->getId();
        }
        return $result;
    }
}
