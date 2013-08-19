<?php
/**
 * Observer that handles webapi permission changes and bridges Magento events to webhook events
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Observer
{
    /** @var Mage_Webhook_Model_Webapi_EventHandler $_webapiEventHandler */
    private $_webapiEventHandler;

    /** @var  Mage_Webhook_Model_Resource_Subscription_Collection $_subscriptionSet */
    private $_subscriptionSet;

    /** @var Mage_Core_Model_Logger */
    private $_logger;

    /**
     * @param Mage_Webhook_Model_Webapi_EventHandler                        $webapiEventHandler
     * @param Mage_Webhook_Model_Resource_Subscription_Collection           $subscriptionSet
     * @param Mage_Core_Model_Logger                                        $logger
     */
    public function __construct(
        Mage_Webhook_Model_Webapi_EventHandler $webapiEventHandler,
        Mage_Webhook_Model_Resource_Subscription_Collection $subscriptionSet,
        Mage_Core_Model_Logger $logger
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
            /** @var Mage_Webhook_Model_Subscription $subscription */
            foreach ($subscriptions as $subscription) {
                $subscription->setStatus(Mage_Webhook_Model_Subscription::STATUS_INACTIVE)
                    ->save();
            }
        } catch (Exception $exception) {
            $this->_logger->logException($exception);
        }
    }

    /**
     * Triggered after webapi user change
     *
     * @param Magento_Event_Observer $observer
     */
    public function afterWebapiUserChange(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     */
    public function afterWebapiRoleChange(Magento_Event_Observer $observer)
    {
        try {
            $model = $observer->getEvent()->getObject();

            $this->_webapiEventHandler->roleChanged($model);
        } catch (Exception $exception) {
            $this->_logger->logException($exception);
        }
    }
}
