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

class Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Address_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_shipping_address_form');
        $this->setTemplate('sales/order/create/shipping/address/form.phtml');
    }

    public function getAddress()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote()->getShippingAddress();
    }

    public function getIsOldCustomer()
    {
        return Mage::getSingleton('adminhtml/quote')->getIsOldCustomer();
    }

    public function getRegionHtmlSelect($type)
    {
        return $this->getParentBlock()->getRegionHtmlSelect($type);
    }

    public function getRegionCollection($type)
    {
        return $this->getParentBlock()->getRegionCollection($type);
    }

    public function getCountryHtmlSelect($type)
    {
        return $this->getParentBlock()->getCountryHtmlSelect($type);
    }

    public function getSameAsBilling()
    {
        return Mage::getSingleton('adminhtml/quote')->getSameAsBilling();
    }

}
