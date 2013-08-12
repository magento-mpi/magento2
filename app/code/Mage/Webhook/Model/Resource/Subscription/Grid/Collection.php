<?php
/**
 * Subscription collection resource for subscription grid
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_Subscription_Grid_Collection
    extends Mage_Webhook_Model_Resource_Subscription_Collection
{
    /**
     * @var Mage_Webhook_Model_Resource_Endpoint
     */
    protected $_endpointResource;

    /**
     * @var Mage_Webhook_Model_Subscription_Config
     */
    protected $_subscriptionConfig;

    /**
     * Collection constructor
     *
     * @param Mage_Webhook_Model_Subscription_Config $subscriptionConfig
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_Webhook_Model_Resource_Endpoint $endpointResource
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Mage_Webhook_Model_Subscription_Config $subscriptionConfig,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_Webhook_Model_Resource_Endpoint $endpointResource,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($fetchStrategy, $endpointResource, $resource);
        $this->_subscriptionConfig = $subscriptionConfig;
        $this->_subscriptionConfig->updateSubscriptionCollection();
    }
}
