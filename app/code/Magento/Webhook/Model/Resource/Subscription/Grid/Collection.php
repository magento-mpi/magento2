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
namespace Magento\Webhook\Model\Resource\Subscription\Grid;

class Collection
    extends \Magento\Webhook\Model\Resource\Subscription\Collection
{
    /**
     * @param Magento_Webhook_Model_Subscription_Config $subscriptionConfig
     * @param Magento_Webhook_Model_Resource_Endpoint $endpointResource
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        Magento_Webhook_Model_Resource_Endpoint $endpointResource,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($endpointResource, $eventManager, $fetchStrategy, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
