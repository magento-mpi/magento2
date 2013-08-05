<?php
/**
 * Provides the access to collection of job items from Magento database under Magento_PubSub_Job_QueueReaderInterface
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Job_QueueReader implements Magento_PubSub_Job_QueueReaderInterface
{
    /**
     * Number of jobs to load at once;
     */
    const PAGE_SIZE = 100;

    /** @var Mage_Webhook_Model_Resource_Job_Collection */
    protected $_collection;

    /** @var ArrayIterator */
    protected $_iterator;

    /**
     * Initialize model
     *
     * @param Mage_Webhook_Model_Resource_Job_Collection $collection
     */
    public function __construct(Mage_Webhook_Model_Resource_Job_Collection $collection)
    {
        $this->_collection = $collection;
        $this->_collection->setPageSize(self::PAGE_SIZE)
            ->setOrder('created_at', Varien_Data_Collection::SORT_ORDER_DESC);
        $this->_collection->addFieldToFilter('status',
                array('in' => array(
                    Magento_PubSub_JobInterface::READY_TO_SEND,
                    Magento_PubSub_JobInterface::RETRY
                )))
            ->addFieldToFilter('retry_at', array('to' => Varien_Date::formatDate(true), 'datetime' => true));
        $this->_iterator = $this->_collection->getIterator();
    }

    /**
     * Return the top job from the queue.
     *
     * @return Magento_PubSub_JobInterface|null
     */
    public function poll()
    {
        if ($this->_iterator->valid()) {
            /** @var $job Magento_PubSub_JobInterface */
            $job = $this->_iterator->current();
            $this->_iterator->next();
            return $job;
        } else if ($this->_collection->getCurPage() < $this->_collection->getLastPageNumber()) {
            $this->_collection->setCurPage($this->_collection->getCurPage() + 1);
            $this->_collection->setPageLimit()
                ->clear();
            $this->_iterator = $this->_collection->getIterator();
            $job = $this->_iterator->current();
            $this->_iterator->next();
            return $job;
        }
        return null;
    }
}
