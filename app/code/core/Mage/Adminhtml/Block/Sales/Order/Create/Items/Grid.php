<?php
/**
 * Adminhtml sales order create items grid block
 *
 * @package     Mage
 * @subpackage  Adminhmtl
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnryi <mitch@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Items_Grid extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search_grid');
        $this->setTemplate('sales/order/create/items/grid.phtml');
//        $this->setRowClickCallback('sc_searchRowClick');
//        $this->setCheckboxCheckCallback('sc_registerSearchProduct');
//        $this->setRowInitCallback('sc_searchRowInit');
//        $this->setDefaultSort('id');
//        $this->setUseAjax(true);
    }

    public function getItems()
    {
        return $this->getParentBlock()->getItems();
    }

    public function getIsOldCustomer()
    {
        return $this->getParentBlock()->getIsOldCustomer();
    }

    public function getSession()
    {
        return $this->getParentBlock()->getSession();
    }

}
