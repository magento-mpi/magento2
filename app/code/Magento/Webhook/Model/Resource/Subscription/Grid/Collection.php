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
     * Collection constructor
     *
     * @param \Magento\Webhook\Model\Subscription\Config $subscriptionConfig
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Webhook\Model\Resource\Endpoint $endpointResource
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Webhook\Model\Subscription\Config $subscriptionConfig,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Webhook\Model\Resource\Endpoint $endpointResource,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        parent::__construct($fetchStrategy, $endpointResource, $resource);
        $subscriptionConfig->updateSubscriptionCollection();
    }
}
