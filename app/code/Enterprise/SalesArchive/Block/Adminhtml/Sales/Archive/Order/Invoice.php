<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive invoice block
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Invoice extends Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = __('Invoices Archive');
    }
}
