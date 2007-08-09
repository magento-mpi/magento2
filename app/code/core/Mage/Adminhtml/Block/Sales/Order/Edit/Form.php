<?php
/**
 * Adminhtml order edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Edit_Form extends Mage_Core_Block_Template //Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_form');
        $this->setTitle(__('Order Information'));
        $this->setTemplate('sales/order/view/plane.phtml');
    }

    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild( 'items', $this->getLayout()->createBlock( 'adminhtml/sales_order_edit_items', 'items.grid' ));
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

//    protected function _prepareForm()
//    {
//        $model = Mage::registry('sales_order');
//
//        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'POST'));
//
//        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));
//
//        if ($model->getId()) {
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
