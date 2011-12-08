<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create totals table block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Table extends Mage_Adminhtml_Block_Template
{

    protected $_websiteCollection = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_totals_table');
    }

}
