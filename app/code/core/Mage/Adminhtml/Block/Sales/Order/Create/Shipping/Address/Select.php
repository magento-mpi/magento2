<?php
/**
 * Adminhtml sales order create shipping address form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Address_Select extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_shipping_address_select');
        $this->setTemplate('sales/order/create/shipping/address/select.phtml');
    }

    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }

    public function getAddresses()
    {
        return $this->getParentBlock()->getSession()->getCustomer()->getLoadedAddressCollection();
    }

    public function getIsSelected($address)
    {
        if ($address->getId() == $this->getParentBlock()->getSession()->getShippingAddressId()) {
            return ' selected';
        }
        return '';
    }

    public function getSameAsBilling()
    {
        return $this->getParentBlock()->getSession()->getSameAsBilling();
    }

    public function toHtml()
    {
        if (! $this->getParentBlock()->customerHasAddresses()) {
            return '';
        }
        return parent::toHtml();
    }

}
