<?php
/**
 * Represents a Job that is used to process events and send messages asynchronously
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PubSub
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_PubSub_JobInterface
{
    /**
     * Status is assigned to newly created Job, identify that it is good to be sent to subscriber
     */
    const STATUS_READY_TO_SEND         = 0;

    /**
     * Status is assigned to the Job when queue handler pick it up for processing
     */
    const STATUS_IN_PROGRESS           = 1;

    /**
     * Status is assigned to the Job when queue handler successfully delivered the job to subscriber
     */
    const STATUS_SUCCEEDED             = 2;

    /**
     * Status is assigned to the Job when queue handler failed to delivered the job after N retries
     */
    const STATUS_FAILED                = 3;

    /**
     * Status is assigned to the Job when queue handler failed to delivered the job but will retry more
     */
    const STATUS_RETRY                 = 4;

    /**
     * Get the event this job is responsible for processing
     *
     * @return Magento_PubSub_EventInterface|null
     */
    public function getEvent();

    /**
     * Return the subscription to send a message to
     *
     * @return Magento_PubSub_SubscriptionInterface|null
     */
    public function getSubscription();

    /**
     * Update the Job status to indicate it has completed successfully
     *
     * @return Magento_PubSub_JobInterface
     */
    public function complete();

    /**
     * Handle retry on failure logic and update job status accordingly.
     *
     * @return Magento_PubSub_JobInterface
     */
    public function handleFailure();

    /**
     * Retrieve the status of the Job
     *
     * @return int
     */
    public function getStatus();

    /**
     * Set the status of the Job
     *
     * @param int $status
     * @return Magento_PubSub_JobInterface
     */
    public function setStatus($status);
}
