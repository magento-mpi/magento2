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
 * Invoices tab
 *
 */

class Magento_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices
     extends Magento_Adminhtml_Block_Sales_Order_View_Tab_Invoices
{

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Magento_SalesArchive_Model_Resource_Order_Invoice_Collection';
    }
}
