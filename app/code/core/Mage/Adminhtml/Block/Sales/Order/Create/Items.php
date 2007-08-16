<?php
/**
 * Adminhtml sales order create items block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_items');
//        $this->setTemplate('sales/order/create/items.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/sales_order_create_items_grid'));
        return parent::_initChildren();
    }

    public function getHeaderText()
    {
        return __('Items Ordered');
    }

    public function getItems()
    {
        return $this->getQuote()->getAllItems();
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => __('Add Product'),
            'onclick' => "$('sc_search').hide();sc_refresh(['search']);this.hide();$('sc_search').show();",
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    public function getHeaderCssClass()
    {
        return 'head-order-items';
    }

    public function toHtml()
    {
        if ($this->getCustomerId() && $this->getStoreId()) {
            return parent::toHtml();
        }
        return '';
    }

}
