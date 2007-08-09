<?php
/**
 * Adminhtml sales order view plane
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_View_Plane extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_plane');
        $this->setTemplate('sales/order/view/plane.phtml');
        $this->setTitle(__('Order Information'));
    }

    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild( 'items', $this->getLayout()->createBlock( 'adminhtml/sales_order_view_items', 'items.grid' ));
        return $this;
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

    public function getOrderDateFormatted($format='short')
    {
        $dateFormatted = strftime(Mage::getStoreConfig('general/local/date_format_' . $format), strtotime($this->getOrder()->getCreatedAt()));
        return $dateFormatted;
    }

    public function getOrderStatus()
    {
        return Mage::getModel('sales/order_status')->load($this->getOrder()->getOrderStatusId())->getFrontendLabel();
    }

}
