<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Resource\Order;

use Magento\Framework\App\Resource;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Resource\Attribute;
use Magento\Sales\Model\Increment as SalesIncrement;
use Magento\Sales\Model\Resource\Entity as SalesResource;
use Magento\Sales\Model\Resource\Order\Invoice\Grid as InvoiceGrid;

/**
 * Flat sales order invoice resource
 */
class Invoice extends SalesResource
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'sales_order_invoice_resource';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_invoice', 'entity_id');
    }

    /**
     * @param Resource $resource
     * @param DateTime $dateTime
     * @param Attribute $attribute
     * @param SalesIncrement $salesIncrement
     * @param InvoiceGrid $gridAggregator
     */
    public function __construct(
        Resource $resource,
        DateTime $dateTime,
        Attribute $attribute,
        SalesIncrement $salesIncrement,
        InvoiceGrid $gridAggregator
    ) {
        parent::__construct($resource, $dateTime, $attribute, $salesIncrement, $gridAggregator);
    }
}
