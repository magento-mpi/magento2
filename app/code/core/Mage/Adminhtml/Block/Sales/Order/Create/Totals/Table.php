<?php
/**
 * Adminhtml sales order create totals table block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Table extends Mage_Core_Block_Template
{

    protected $_websiteCollection = null;

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_totals_table');
        $this->setTemplate('sales/order/create/totals/table.phtml');
    }

}
