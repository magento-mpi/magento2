<?php
/**
 * Adminhtml sales invoice view plane
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_View_Plane extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('invoice_plane');
        $this->setTemplate('sales/invoice/view/plane.phtml');
        $this->setTitle(__('Invoice Information'));
        $model = Mage::registry('sales_entity');
        if ($model instanceof Mage_Sales_Model_Invoice) {
           Mage::register('sales_order', Mage::getModel('sales/order')->load($model->getOrderId()));
        }
    }

    public function getOrder()
    {
        $model = Mage::registry('sales_entity');
        if ($model instanceof Mage_Sales_Model_Invoice) {
            return Mage::getModel('sales/order')->load($model->getOrderId());
        }
        return $model;
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

}
