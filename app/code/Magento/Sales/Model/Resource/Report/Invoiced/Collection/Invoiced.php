<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales report invoiced collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report\Invoiced\Collection;

class Invoiced
    extends \Magento\Sales\Model\Resource\Report\Invoiced\Collection\Order
{
    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Sales_Model_Resource_Report $resource
     */
    public function __construct(
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        \Magento\Sales\Model\Resource\Report $resource
    ) {
        $resource->init('sales_invoiced_aggregated');
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }
}
