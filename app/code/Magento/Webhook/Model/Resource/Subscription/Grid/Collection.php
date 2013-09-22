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
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($endpointResource, $eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
