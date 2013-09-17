<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Index_Model_EventRepository
{
    /**
     * Event collection factory
     *
     * @var Magento_Index_Model_Resource_Event_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Index_Model_Resource_Event_CollectionFactory $collectionFactory
     */
    public function __construct(Magento_Index_Model_Resource_Event_CollectionFactory $collectionFactory)
    {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Check whether unprocessed events exist for provided process
     *
     * @param int|array|Magento_Index_Model_Process $process
     * @return bool
     */
    public function hasUnprocessed($process)
    {
        return (bool) $this->getUnprocessed($process)->getSize();
    }

    /**
     * Retrieve list of unprocessed events
     *
     * @param int|array|Magento_Index_Model_Process $process
     * @return Magento_Index_Model_Resource_Event_Collection
     */
    public function getUnprocessed($process)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addProcessFilter($process, Magento_Index_Model_Process::EVENT_STATUS_NEW);
        return $collection;
    }
}
