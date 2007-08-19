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

class Mage_Adminhtml_Block_Sales_Cmemo_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_cmemo';
        $this->_mode = 'create';

        parent::__construct();

        $this->_updateButton('save', 'label', __('Submit Credit Memo'));
        $this->_removeButton('delete');
    }

    public function getHeaderText()
    {
        return __('New Credit Memo for Invoice #') . Mage::registry('sales_invoice')->getIncrementId();
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/sales_order/invoice', array('invoice_id' => $this->getRequest()->getParam('invoice_id')));
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('*/sales_invoice/savecmnew', array('invoice_id' => $this->getRequest()->getParam('invoice_id')));
    }

    public function getInvoice()
    {
        return Mage::registry('sales_invoice');
    }

    public function getInvoiceDateFormatted()
    {
        return $this->getInvoice()->getCreatedAt();
        return $date;
    }

}
