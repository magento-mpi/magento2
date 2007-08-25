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
class Mage_Adminhtml_Block_Sales_Order_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'view';

        parent::__construct();

        $this->_updateButton('edit', 'label', __('Edit Order'));
        if (Mage::registry('sales_order')->getOrderStatusId() == 4) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', __('Cancel Order'));
        }

        $this->_removeButton('reset');
        $this->_removeButton('save');

        // TODO - Next update functionality, not QA'ed yet
        // $this->_addButton('edit', array(
            // 'label' => __('Edit Order'),
            // 'onclick'   => 'deleteConfirm(\''. __('Are you sure? This order will be cancelled and a new one will be created instead') .'\', \'' . $this->getEditUrl() . '\')',
        // ));

        $this->_addButton('backordered', array(
            'label' => __('Edit Order Status'),
            'onclick'   => 'window.location.href=\'' . $this->getEditBackorderedUrl() . '\'',
        ));

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

    public function getEditUrl()
    {
        return Mage::getUrl('*/sales_order_create/edit', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

    public function getEditBackorderedUrl()
    {
        return Mage::getUrl('*/*/edit', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

}
