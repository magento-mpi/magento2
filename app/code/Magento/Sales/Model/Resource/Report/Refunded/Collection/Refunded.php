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
 * Sales report refunded collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Report\Refunded\Collection;

class Refunded
    extends \Magento\Sales\Model\Resource\Report\Refunded\Collection\Order
{
    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Sales\Model\Resource\Report $resource
    ) {
        $resource->init('sales_refunded_aggregated');
        parent::__construct($eventManager, $fetchStrategy, $resource);
    }
}
