<?php
/**
 * Adminhtml sales invoice view
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_View extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_invoice';

        $this->_mode = 'view';

        parent::__construct();


        $this->_removeButton('delete');
        $this->_removeButton('save');
        $this->_removeButton('reset');

//        $this->_addButton('edit', array(
//            'label' => __('Edit Invoice'),
//            'onclick'   => 'window.location.href=\'' . $this->getEditUrl() . '\'',
//        ));

        $this->_addButton('credit_memo', array(
            'label' => __('Create Credit Memo'),
            'onclick'   => 'window.location.href=\'' . $this->getCreateMemoUrl() . '\'',
            'class' => 'add',
        ));

        $this->setId('sales_invoice_view');
    }

    public function getHeaderText()
    {
        return __('Invoice #') . Mage::registry('sales_invoice')->getIncrementId();
    }

    public function getCreateMemoUrl()
    {
        return Mage::getUrl('*/sales_invoice/cmemo', array('invoice_id' => $this->getRequest()->getParam('invoice_id')));
    }

    public function getEditUrl()
    {
        return Mage::getUrl('*/sales_invoice/edit', array('invoice_id' => $this->getRequest()->getParam('invoice_id')));
    }

}
