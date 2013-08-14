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
interface Magento_PubSub_EventInterface
{
    /**
     * Status codes for events
     */
    const READY_TO_SEND = 0;
    const IN_PROGRESS   = 1;
    const PROCESSED     = 2;

    /**
     * Returns the status code of the event. Status indicates if the event has been processed
     * or not.
     *
     * @return int
     */
    public function getStatus();

    /**
     * Returns a PHP array of data that represents what should be included in the message body.
     *
     * @return array
     */
    public function getBodyData();

    /**
     * Prepare headers before return
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Returns a PHP string representing the topic of WebHook
     *
     * @return string
     */
    public function getTopic();

    /**
     * Mark event as processed
     *
     * @return Magento_PubSub_EventInterface
     */
    public function markAsProcessed();
}
