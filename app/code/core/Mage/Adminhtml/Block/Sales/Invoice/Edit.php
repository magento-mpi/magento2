<?php
/**
 * Adminhtml invoice edit
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Invoice_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'invoice_id';
        $this->_controller = 'sales_invoice';

        parent::__construct();

        $model = Mage::registry('sales_entity');
        $this->_updateButton('save', 'label', ($model instanceof Mage_Sales_Model_Invoice) ? __('Save Invoice') : __('Submit Invoice'));
        $this->_updateButton('delete', 'label', __('Delete Invoice'));
    }

    public function getHeaderText()
    {
        $model = Mage::registry('sales_entity');
        if ($model instanceof Mage_Sales_Model_Invoice) {
            return __('Edit Invoice #') . " '" . Mage::registry('sales_entity')->getEntityId() . "'";
        }
        else {
            return __('New Invoice for Order #') . Mage::registry('sales_entity')->getRealOrderId();
        }
    }

    public function getBackUrl()
    {
        $model = Mage::registry('sales_entity');
        if ($model instanceof Mage_Sales_Model_Invoice) {
            return parent::getBackUrl();
        }
        return Mage::getUrl('*/sales_order/view', array('order_id' => $this->getRequest()->getParam('order_id')));
    }

}
