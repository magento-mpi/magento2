<?php
/**
 * Observer that handles webapi permission changes and bridges Magento events to webhook events
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Observer
{
    /** @var Magento_Webhook_Model_Webapi_EventHandler $_webapiEventHandler */
    private $_webapiEventHandler;

    /** @var  Magento_Webhook_Model_Resource_Subscription_Collection $_subscriptionSet */
    private $_subscriptionSet;

    /** @var Magento_Core_Model_Logger */
    private $_logger;

    /**
     * @param Magento_Webhook_Model_Webapi_EventHandler                        $webapiEventHandler
     * @param Magento_Webhook_Model_Resource_Subscription_Collection           $subscriptionSet
     * @param Magento_Core_Model_Logger                                        $logger
     */
    public function __construct(
        Magento_Webhook_Model_Webapi_EventHandler $webapiEventHandler,
        Magento_Webhook_Model_Resource_Subscription_Collection $subscriptionSet,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_webapiEventHandler = $webapiEventHandler;
        $this->_subscriptionSet = $subscriptionSet;
        $this->_logger = $logger;
    }

    /**
     * Triggered after webapi user deleted. It updates status of the activated subscriptions
     * associated with this webapi user to inactive
     */
    public function afterWebapiUserDelete()
    {
        try {
            $subscriptions = $this->_subscriptionSet->getActivatedSubscriptionsWithoutApiUser();
            /** @var Magento_Webhook_Model_Subscription $subscription */
            foreach ($subscriptions as $subscription) {
                $subscription->setStatus(Magento_Webhook_Model_Subscription::STATUS_INACTIVE)
                    ->save();
            }
        } catch (Exception $exception) {
            $this->_logger->logException($exception);
        }
    }

    /**
     * Triggered after webapi user change
     *
     * @param \Magento\Event\Observer $observer
     */
    public function afterWebapiUserChange(\Magento\Event\Observer $observer)
    {
        try {
            $model = $observer->getEvent()->getObject();

            $this->_webapiEventHandler->userChanged($model);
        } catch (Exception $exception) {
            $this->_logger->logException($exception);
        }
    }

    /**
     * Triggered after webapi role change
     *
     * @param \Magento\Event\Observer $observer
     */
    public function afterWebapiRoleChange(\Magento\Event\Observer $observer)
    {
        try {
            $model = $observer->getEvent()->getObject();

            $this->_webapiEventHandler->roleChanged($model);
        } catch (Exception $exception) {
            $this->_logger->logException($exception);
        }
    }
}
