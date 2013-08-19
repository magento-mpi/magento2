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
        }
        return null;
    }
}
