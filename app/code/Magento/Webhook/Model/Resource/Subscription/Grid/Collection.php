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
     * @param \Magento\Webhook\Model\Resource\Endpoint $endpointResource
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($endpointResource, $eventManager, $fetchStrategy, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
