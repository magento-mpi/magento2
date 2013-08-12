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
     * Status codes for job
     */
    const READY_TO_SEND         = 0;
    const SUCCESS               = 1;
    const FAILED                = 2;
    const RETRY                 = 3;

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
     * Process response and update Job status accordingly.
     *
     * @param Magento_Outbound_Transport_Http_Response $response
     */
    public function handleResponse($response);

    /**
     * Handle retry on failure logic and update job status accordingly.
     */
    public function handleFailure();
}