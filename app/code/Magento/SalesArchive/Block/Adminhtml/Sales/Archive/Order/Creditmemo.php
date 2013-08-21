<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive shipment block
 *
 */

class Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Creditmemo
    extends Magento_SalesArchive_Block_Adminhtml_Sales_Archive_Order_Container
{
    protected function _construct()
    {
        $this->_controller = 'sales_creditmemo';
        $this->_headerText = __('Credit Memos Archive');
    }
}
