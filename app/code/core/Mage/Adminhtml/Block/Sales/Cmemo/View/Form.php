<?php
/**
 * Adminhtml invoice view form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Cmemo_View_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('invoice_form');
        $this->setTitle(__('Credit Memo Information'));
        $this->setTemplate('sales/cmemo/view.phtml');
    }

    public function getInvoice()
    {
        return Mage::registry('sales_invoice');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('items', $this->getLayout()->createBlock( 'adminhtml/sales_cmemo_view_items', 'sales_cmemo_view_items'));
        return $this;
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

    public function getInvoiceDateFormatted($format='short')
    {
        $dateFormatted = strftime(Mage::getStoreConfig('general/local/date_format_' . $format), strtotime($this->getInvoice()->getCreatedAt()));
        return $dateFormatted;
    }

}
