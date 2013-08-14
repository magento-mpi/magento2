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
 * Creditmemos tab
 *
 */

class Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos
     extends Magento_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos
{
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'Enterprise_SalesArchive_Model_Resource_Order_Creditmemo_Collection';
    }
}
