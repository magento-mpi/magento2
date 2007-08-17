<?php
/**
 * Adminhtml sales order create billing address block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_billing_address');
    }

    protected function _initChildren()
    {
        $this->setChild('select', $this->getLayout()->createBlock('adminhtml/sales_order_create_billing_address_select'));
        $this->setChild('form', $this->getLayout()->createBlock('adminhtml/sales_order_create_billing_address_form'));
        return parent::_initChildren();
    }

    public function getHeaderText()
    {
        return __('Billing Address');
    }

    public function getHeaderCssClass()
    {
        return 'head-billing-address';
    }

    public function toHtml()
    {
        if (! $this->getSession()->getStoreId()) {
            return '';
        }
        return parent::toHtml();
    }

}
