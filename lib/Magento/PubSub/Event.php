<?php
/**
 * Represents a PubSub event to be dispatched
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PubSub;

class Event implements \Magento\PubSub\EventInterface
{
    /** @var int */
    protected $_status = \Magento\PubSub\EventInterface::STATUS_READY_TO_SEND;

    /** @var array */
    protected $_bodyData;

    /** @var array */
    protected $_headers = array();

    /** @var string */
    protected $_topic;

    /**
     * @param $topic
     * @param $bodyData
     */
    public function __construct($topic, $bodyData)
    {
        $this->_topic = $topic;
        $this->_bodyData = $bodyData;
    }

    /**
     * Returns the status code of the event. Status indicates if the event has been processed
     * or not.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Returns a PHP array of data that represents what should be included in the message body.
     *
     * @return array
     */
    public function getBodyData()
    {
        return $this->_bodyData;
    }

    /**
     * Prepare headers before return
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Returns a PHP string representing the topic of WebHook
     *
     * @return string
     */
    public function getTopic()
    {
        return $this->_topic;
    }

    /**
     * Mark event as processed
     *
     * @return \Magento\PubSub\Event
     */
    public function complete()
    {
        $this->_status = \Magento\PubSub\EventInterface::STATUS_PROCESSED;
        return $this;
    }

    /**
     * Mark event as processed
     *
     * @return \Magento\PubSub\Event
     */
    public function markAsInProgress()
    {
        $this->_status = \Magento\PubSub\EventInterface::STATUS_IN_PROGRESS;
        return $this;
    }
}
