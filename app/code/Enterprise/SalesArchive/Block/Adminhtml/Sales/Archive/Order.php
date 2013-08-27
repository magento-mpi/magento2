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
 * Archive orders block
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order extends Enterprise_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = __('Orders Archive');
    }
}
