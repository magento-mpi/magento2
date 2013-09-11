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
 * Flat sales order invoice comment collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Order\Invoice\Comment;

class Collection
    extends \Magento\Sales\Model\Resource\Order\Comment\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_invoice_comment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Sales\Model\Order\Invoice\Comment', 'Magento\Sales\Model\Resource\Order\Invoice\Comment');
    }

    /**
     * Set invoice filter
     *
     * @param int $invoiceId
     * @return \Magento\Sales\Model\Resource\Order\Invoice\Comment\Collection
     */
    public function setInvoiceFilter($invoiceId)
    {
        return $this->setParentFilter($invoiceId);
    }
}
