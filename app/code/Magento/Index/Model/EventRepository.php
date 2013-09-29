<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Index\Model;

class EventRepository
{
    /**
     * Event collection factory
     *
     * @var \Magento\Index\Model\Resource\Event\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Index\Model\Resource\Event\CollectionFactory $collectionFactory
     */
    public function __construct(\Magento\Index\Model\Resource\Event\CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Check whether unprocessed events exist for provided process
     *
     * @param int|array|\Magento\Index\Model\Process $process
     * @return bool
     */
    public function hasUnprocessed($process)
    {
        return (bool) $this->getUnprocessed($process)->getSize();
    }

    /**
     * Retrieve list of unprocessed events
     *
     * @param int|array|\Magento\Index\Model\Process $process
     * @return \Magento\Index\Model\Resource\Event\Collection
     */
    public function getUnprocessed($process)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addProcessFilter($process, \Magento\Index\Model\Process::EVENT_STATUS_NEW);
        return $collection;
    }
}
