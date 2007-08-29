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
 * Adminhtml sales order create billing address form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Address_Form extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_billing_address_form');
        $this->setTemplate('sales/order/create/billing/address/form.phtml');
    }

    public function getAddress()
    {
        return Mage::getSingleton('adminhtml/quote')->getQuote()->getBillingAddress();
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

}
