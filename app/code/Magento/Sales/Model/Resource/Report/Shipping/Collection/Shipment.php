<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Report\Shipping\Collection;

/**
 * Sales report shipping collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shipment extends Order
{
    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\Resource\Report $resource
     * @param \Zend_Db_Adapter_Abstract $connection
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\Resource\Report $resource,
        $connection = null
    ) {
        $resource->init('sales_shipping_aggregated');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }
}
