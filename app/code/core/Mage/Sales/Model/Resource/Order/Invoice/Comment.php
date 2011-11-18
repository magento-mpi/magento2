<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order invoice comment resource
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Invoice_Comment extends Mage_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_invoice_comment_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_invoice_comment', 'entity_id');
    }
}
