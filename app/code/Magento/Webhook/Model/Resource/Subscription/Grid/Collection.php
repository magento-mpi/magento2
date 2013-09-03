<?php
/**
 * Subscription collection resource for subscription grid
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_Subscription_Grid_Collection
    extends Magento_Webhook_Model_Resource_Subscription_Collection
{

    /**
     * Collection constructor
     *
     * @param Magento_Webhook_Model_Subscription_Config $subscriptionConfig
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param Magento_Webhook_Model_Resource_Endpoint $endpointResource
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Webhook_Model_Subscription_Config $subscriptionConfig,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Webhook_Model_Resource_Endpoint $endpointResource,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($fetchStrategy, $endpointResource, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
