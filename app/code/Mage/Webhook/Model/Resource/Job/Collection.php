<?php
/**
 * Job collection resource
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Job_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Number of jobs to load at once;
     */
    const PAGE_SIZE = 100;

    /**
     * Initialize Collection
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Webhook_Model_Job', 'Mage_Webhook_Model_Resource_Job');
    }

    /**
     * Adds FOR UPDATE lock on retrieved rows and filter status
     *
     * @return Mage_Webhook_Model_Resource_Job_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->forUpdate(true);

        $this->addFieldToFilter('status', array(
                'in' => array(
                    Magento_PubSub_JobInterface::READY_TO_SEND,
                    Magento_PubSub_JobInterface::RETRY
                )))
            ->addFieldToFilter('retry_at', array('to' => Magento_Date::formatDate(true), 'datetime' => true))
            ->setOrder('updated_at', Magento_Data_Collection::SORT_ORDER_ASC)
            ->setPageSize(self::PAGE_SIZE);
        return $this;
    }

    /**
     * Start transaction before executing the query in order to update the status atomically
     *
     * @return Mage_Webhook_Model_Resource_Job_Collection
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
     * @return Mage_Webhook_Model_Resource_Job_Collection
     * @throws Exception
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        try {
            $loadedIds = $this->_getLoadedIds();
            if (!empty($loadedIds)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => Magento_PubSub_JobInterface::IN_PROGRESS),
                    array('dispatch_job_id IN (?)' => $loadedIds));
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

    /**
     * Change job status back to READY_TO_SEND if stays in IN_PROGRESS longer than hour
     *
     * Regularly run by scheduling mechanism
     *
     * @throws Exception
     * @return null
     */
    public function revokeIdlingInProgress()
    {
        $this->getConnection()->beginTransaction();
        try {
            /* if event is in progress state for less than hour we do nothing with it*/
            $appropriateLastUpdatedTime = time() - 60*60;
            $this->addFieldToFilter('status', Magento_PubSub_JobInterface::IN_PROGRESS)
                ->addFieldToFilter('updated_at', array('to' => Magento_Date::formatDate($appropriateLastUpdatedTime),
                    'datetime' => true));
            $idsToRevoke = $this->_getLoadedIds();
            if (count($idsToRevoke)) {
                $this->getConnection()->update($this->getMainTable(),
                    array('status' => Magento_PubSub_JobInterface::READY_TO_SEND),
                    array('event_id IN (?)' => $idsToRevoke));
            }
        } catch (Exception $e) {
            $this->getConnection()->rollBack();
            $this->clear();
            throw $e;
        }
        $this->getConnection()->commit();
    }
}
