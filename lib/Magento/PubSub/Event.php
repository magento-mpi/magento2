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
class Magento_PubSub_Event implements Magento_PubSub_EventInterface
{
    /** @var int */
    protected $_status = Magento_PubSub_EventInterface::PREPARING;

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
     * Mark event as ready to send
     *
     * @return Magento_PubSub_EventInterface
     */
    public function markAsReadyToSend()
    {
        $this->_status = Magento_PubSub_EventInterface::READY_TO_SEND;
    }

    /**
     * Mark event as processed
     *
     * @return Magento_PubSub_EventInterface
     */
    public function markAsProcessed()
    {
        $this->_status = Magento_PubSub_EventInterface::PROCESSED;
    }
}