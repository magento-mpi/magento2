<?php
/**
 * Adminhtml sales order create billing address form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address_Select extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_billing_address_select');
        $this->setTemplate('sales/order/create/billing/address/select.phtml');
    }

    public function getQuote()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote();
    }

    public function getAddresses()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote()->getCustomer()->getLoadedAddressCollection();
    }

    public function getIsSelected($address)
    {
        if ($address->getId() == Mage::getSingleton('adminhtml/quote')->getBillingAddressId()) {
            return ' selected';
        }
        return '';
    }

    public function toHtml()
    {
        if (! $this->getParentBlock()->customerHasAddresses()) {
            return '';
        }
        return parent::toHtml();
    }

}
