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
 * Invoices tab
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Invoices
     extends Magento_Adminhtml_Block_Sales_Order_View_Tab_Invoices
{

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Enterprise_SalesArchive_Model_Resource_Order_Invoice_Collection';
    }
}
