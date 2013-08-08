<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Enterprise_Queue_Model_Task extends Mage_Core_Model_Abstract
{
    /**
     * Percentage of task completion
     *
     * @var int
     */
    protected $_percentage = 0;

    /**
     * Is task in queue
     *
     * @var bool
     */
    protected $_isEnqueued = false;

    /**
     * @param Mage_Core_Model_Context $context
     * @param Enterprise_Queue_Model_Resource_Task $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Enterprise_Queue_Model_Resource_Task $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Set task status information
     *
     * @param array $status
     */
    public function setStatus(array $status)
    {
        $this->_isEnqueued = $status['isEnqueued'];
        $this->_percentage = $status['percentage'];
    }

    /**
     * Check whether task is in queue
     *
     * @return bool
     */
    public function isEnqueued()
    {
        return $this->_isEnqueued;
    }

    /**
     * Retrieve task completion
     *
     * @return int
     */
    public function getPercentage()
    {
        return $this->_percentage;
    }
}
