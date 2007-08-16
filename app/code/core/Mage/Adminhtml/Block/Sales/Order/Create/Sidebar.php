<?php
/**
 * Adminhtml sales order create sidebar
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_sidebar');
        $this->setTemplate('sales/order/create/sidebar.phtml');
    }

    protected function _initChildren()
    {
        $this->setChild('cart', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_cart'));
        $this->setChild('wishlist', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_wishlist'));
        $this->setChild('viewed', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_viewed'));
        $this->setChild('compared', $this->getLayout()->createBlock('adminhtml/sales_order_create_sidebar_compared'));
        return parent::_initChildren();
    }

}
