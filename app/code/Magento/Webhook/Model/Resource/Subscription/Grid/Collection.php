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
     * @param \Magento\Webhook\Model\Subscription\Config $subscriptionConfig
     * @param \Magento\Webhook\Model\Resource\Endpoint $endpointResource
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Core\Model\Resource\Db\Abstract $resource
     */
    public function __construct(
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($endpointResource, $eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
