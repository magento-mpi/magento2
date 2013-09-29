<?php
/**
 * Fulfills event queueing functionality for Magento,
 * wrapper around Magento collection with Event QueueReader Interface.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Event;

class QueueReader implements \Magento\PubSub\Event\QueueReaderInterface
{
    /** @var \ArrayIterator */
    protected $_iterator;

    /**
     * Initialize collection representing the queue
     *
     * @param \Magento\Webhook\Model\Resource\Event\Collection $collection
     */
    public function __construct(\Magento\Webhook\Model\Resource\Event\Collection $collection)
    {
        $this->_iterator = $collection->getIterator();
    }

    /**
     * Get the top event from the queue.
     *
     * @return \Magento\PubSub\EventInterface|null
     */
    public function poll()
    {
        if ($this->_iterator->valid()) {
            /** @var \Magento\Webhook\Model\Event $event */
            $event = $this->_iterator->current();
            $this->_iterator->next();
            return $event;
        }
        return null;
    }
}
