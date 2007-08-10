<?php
/**
 * Adminhtml invoice create
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_invoice';
        $this->_mode = 'create';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Submit Invoice'));
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        return __('New Invoice for Order #') . Mage::registry('sales_invoice')->getOrder()->getRealOrderId();
    }

    public function getBackUrl()
    {
        if (Mage_Sales_Model_Invoice::TYPE_INVOICE == Mage::registry('sales_invoice')->getInvoiceType()) {
            return Mage::getUrl('*/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
        }
        return Mage::getUrl('*/sales_order/cmemo', array('invoice_id' => $this->getRequest()->getParam('invoice_id')));
    }

}
