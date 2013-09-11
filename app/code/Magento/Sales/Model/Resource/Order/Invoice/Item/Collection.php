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
 * Flat sales order invoice item collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Item;

class Collection extends \Magento\Sales\Model\Resource\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_invoice_item_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Order\Invoice\Item', 'Magento\Sales\Model\Resource\Order\Invoice\Item');
    }

    /**
     * Set invoice filter
     *
     * @param int $invoiceId
     * @return \Magento\Sales\Model\Resource\Order\Invoice\Item\Collection
     */
    public function setInvoiceFilter($invoiceId)
    {
        $this->addFieldToFilter('parent_id', $invoiceId);
        return $this;
    }
}
