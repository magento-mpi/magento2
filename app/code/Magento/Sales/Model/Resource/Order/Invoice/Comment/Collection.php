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
class Magento_Sales_Model_Resource_Order_Invoice_Comment_Collection
    extends Magento_Sales_Model_Resource_Order_Comment_Collection_Abstract
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
        $this->_init('Magento_Sales_Model_Order_Invoice_Comment', 'Magento_Sales_Model_Resource_Order_Invoice_Comment');
    }

    /**
     * Set invoice filter
     *
     * @param int $invoiceId
     * @return Magento_Sales_Model_Resource_Order_Invoice_Comment_Collection
     */
    public function setInvoiceFilter($invoiceId)
    {
        return $this->setParentFilter($invoiceId);
    }
}
