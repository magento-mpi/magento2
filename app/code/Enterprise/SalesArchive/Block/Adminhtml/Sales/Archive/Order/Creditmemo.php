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
 * Archive shipment block
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Creditmemo
    extends Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_creditmemo';
        $this->_headerText = __('Credit Memos Archive');
    }
}
