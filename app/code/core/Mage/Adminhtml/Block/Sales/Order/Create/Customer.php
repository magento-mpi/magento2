<?php
/**
 * Adminhtml sales order create block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Customer extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_customer');
    }

    protected function _initChildren()
    {
        $this->setChild('grid', $this->getLayout()->createBlock('adminhtml/sales_order_create_customer_grid'));
        return parent::_initChildren();
    }

    public function getHeaderText()
    {
        return __('Please select a customer');
    }

    public function toHtml()
    {
        if ($this->getSession()->getCustomerId()) {
            return '';
        }
        return parent::toHtml();
    }

    public function getButtonsHtml()
    {
        $addButtonData = array(
            'label' => __('Create New Customer'),
            'onclick' => "sc_customerId='new';sc_refresh(['customer','store'],{customer_id:'new'})",
            'class' => 'add',
        );
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

}
