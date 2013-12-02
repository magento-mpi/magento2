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
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Webhook\Model\Resource\Endpoint $endpointResource
     * @param \Magento\Webhook\Model\Subscription\Config $subscriptionConfig
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $endpointResource,
            $connection,
            $resource
        );
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
