<?php
/**
 * Adminhtml sales order view
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_View extends Mage_Adminhtml_Block_Widget_View_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';

        parent::__construct();

        $this->_updateButton('edit', 'label', __('Edit Order'));
        $this->_addButton('invoice', array(
            'label' => __('Create New Invoice'),
            'onclick'   => 'window.location.href=\'' . $this->getCreateInvoiceUrl() . '\'',
            'class' => 'add',
        ));

        $this->setId('sales_order_view');
    }

    public function getHeaderText()
    {
        return __('Order #') . Mage::registry('sales_order')->getRealOrderId();
    }

    public function getCreateInvoiceUrl()
    {
        return Mage::getUrl('*/sales_invoice/new', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

}
