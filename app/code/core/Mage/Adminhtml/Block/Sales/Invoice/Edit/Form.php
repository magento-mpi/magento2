<?php
/**
 * Adminhtml invoice edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_Edit_Form extends Mage_Core_Block_Template //Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('invoice_form');
        $this->setTitle(__('Invoice Information'));
        $this->setTemplate('sales/order/view/plane.phtml');
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
        $this->setChild( 'items', $this->getLayout()->createBlock( 'adminhtml/sales_invoice_edit_items', 'items.grid' ));
        return $this;
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

//    protected function _prepareForm()
//    {
//        $model = Mage::registry('sales_entity');
//
//        $isInvoice = ('invoice' === $model->getType());
//
//        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));
//
//        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));
//
//        if ($isInvoice) {
//        	$fieldset->addField('invoice_id', 'hidden', array(
//                'name' => 'invoice_id',
//            ));
//        } else {
//        	$fieldset->addField('order_id', 'hidden', array(
//                'name' => 'order_id',
//            ));
//        }
//
//        $form->setUseContainer(true);
//
//        $this->setForm($form);
//
//        return parent::_prepareForm();
//    }

}
