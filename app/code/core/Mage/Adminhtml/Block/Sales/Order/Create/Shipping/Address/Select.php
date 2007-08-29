<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order create shipping address form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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
