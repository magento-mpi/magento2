<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\SalesArchive\Model\Order\Shipment;

class CollectionUpdater implements \Magento\Framework\View\Layout\Argument\UpdaterInterface
{
    /**
     * @var \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
     */
    protected $orderItem;

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\AbstractOrder $orderItem
     */
    public function __construct(\Magento\Sales\Block\Adminhtml\Order\AbstractOrder $orderItem)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @param \Magento\Sales\Model\Resource\Order\Shipment\Grid\Collection $argument
     * @return \Magento\Sales\Model\Resource\Order\Shipment\Grid\Collection
     */
    public function update($argument)
    {
        if ($this->orderItem->getOrder()->getIsArchived()) {
            $argument->setMainTable('magento_sales_shipment_grid_archive');
        }
        return $argument;
    }
}
