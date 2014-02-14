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

interface EventInterface
{
    /**
     * Status is assigned to newly created Event, identify that it is good to be sent to subscribers
     */
    const STATUS_READY_TO_SEND = 0;

    /**
     * Status is assigned to event when queue handler pick it up for processing
     */
    const STATUS_IN_PROGRESS   = 1;

    /**
     * Status is assigned to event when queue handler successfully processed the event
     */
    const STATUS_PROCESSED     = 2;

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
     * @return \Magento\PubSub\EventInterface
     */
    public function complete();

    /**
     * Mark event as In Progress
     *
     * @return \Magento\PubSub\Event
     */
    public function markAsInProgress();
}
