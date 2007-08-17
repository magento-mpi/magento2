<?php
/**
 * Adminhtml sales order create
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order';
        $this->_mode = 'create';

        parent::__construct();

        $this->setId('sales_order_create');

        $this->_updateButton('save', 'label', __('Save Order'));
        $this->_updateButton('save', 'onclick', '$(\'edit_form\').submit()');

        $this->_removeButton('back');

        $this->_updateButton('reset', 'label', __('Cancel Order'));
        $this->_updateButton('reset', 'onclick', 'deleteConfirm(\''. __('Are you sure you want to cancel this order?') .'\', \'' . $this->getCancelUrl() . '\')');

    }

    public function getHeaderHtml()
    {
        $customer = Mage::getSingleton('adminhtml/quote')->getCustomer();
        $out = '<h3>' . $this->getHeaderText() . ' <span id="sc_customer_name"';
        if (!$customer->getId()) {
            $out .= ' style="display: none;"';
        }
        $out .= '>';
        if ($customer->getId()) {
            $out .= __('for') . ' ' . $customer->getName();
        }
        $out .= '</span> <span style="display: none;" id="sc_store_name"></span></h3>';
        return $out;
    }

    public function getHeaderText()
    {
        return __('Create New Order');
    }

    public function getHeaderWidth()
    {
        return 'width: 70%;';
    }

    public function getCancelUrl()
    {
        return Mage::getUrl('*/*/cancel', array('quote_id' => $this->getRequest()->getParam('quote_id')));
    }

}
