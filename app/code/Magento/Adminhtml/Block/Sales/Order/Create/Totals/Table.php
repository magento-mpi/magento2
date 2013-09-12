<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals table block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Totals_Table extends Magento_Adminhtml_Block_Template
{

    protected $_websiteCollection = null;

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_totals_table');
    }

}
