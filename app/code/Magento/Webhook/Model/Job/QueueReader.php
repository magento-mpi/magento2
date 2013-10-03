<?php
/**
 * Provides the access to collection of job items from Magento database under \Magento\PubSub\Job\QueueReaderInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Job;

class QueueReader implements \Magento\PubSub\Job\QueueReaderInterface
{
    /** @var \Magento\Webhook\Model\Resource\Job\Collection */
    protected $_collection;

    /** @var \ArrayIterator */
    protected $_iterator;

    /**
     * Initialize model
     *
     * @param \Magento\Webhook\Model\Resource\Job\Collection $collection
     */
    public function __construct(\Magento\Webhook\Model\Resource\Job\Collection $collection)
    {
        $this->_collection = $collection;
        $this->_iterator = $this->_collection->getIterator();
    }

    /**
     * Return the top job from the queue.
     *
     * @return \Magento\PubSub\JobInterface|null
     */
    public function poll()
    {
        if ($this->_iterator->valid()) {
            /** @var $job \Magento\PubSub\JobInterface */
            $job = $this->_iterator->current();
            $this->_iterator->next();
            return $job;
        }
        return null;
    }
}
