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
 * Sales report shipping collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report\Shipping\Collection;

class Shipment
    extends \Magento\Sales\Model\Resource\Report\Shipping\Collection\Order
{
    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Sales_Model_Resource_Report $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Sales\Model\Resource\Report $resource
    ) {
        $resource->init('sales_shipping_aggregated');
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }
}
