<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Shopping Cart items grid
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Items extends Enterprise_Enterprise_Block_Adminhtml_Template
{
    public function getJsObjectName()
    {
        return 'checkoutItemsGrid';
    }

    protected function _prepareCollection()
    {
        $collection = $this->getQuote()->getItemsCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function getItems()
    {
        return $this->getQuote()->getItemsCollection();
    }

    protected function getCustomerId()
    {
        return $this->getCustomer()->getId();
    }

    /**
     * Check if we need display grid totals include tax
     *
     * @return bool
     */
    public function displayTotalsIncludeTax()
    {
        $res = Mage::getSingleton('tax/config')->displayCartSubtotalInclTax($this->getStore())
            || Mage::getSingleton('tax/config')->displayCartSubtotalBoth($this->getStore());
        return $res;
    }

    public function getSubtotal()
    {
        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        }
        else {
            $address = $this->getQuote()->getShippingAddress();
        }
        if ($this->displayTotalsIncludeTax()) {
            return $address->getSubtotal()+$address->getTaxAmount();
        } else {
            return $address->getSubtotal();
        }
        return false;
    }

    public function getSubtotalWithDiscount()
    {
        $address = $this->getQuote()->getShippingAddress();
        if ($this->displayTotalsIncludeTax()) {
            return $address->getSubtotal()+$address->getTaxAmount()+$this->getDiscountAmount();
        } else {
            return $address->getSubtotal()+$this->getDiscountAmount();
        }
    }

    public function getDiscountAmount()
    {
        return $this->getQuote()->getShippingAddress()->getDiscountAmount();
    }

    /**
     * Retrieve formated price
     *
     * @param   decimal $value
     * @return  string
     */
    public function formatPrice($value)
    {
        return $this->getStore()->formatPrice($value);
    }

    public function usedCustomPriceForItem($item)
    {
        return false;
    }

    /**
     * Retrieve formated price
     *
     * @param   decimal $value
     * @return  string
     */
    public function isAllowedActionColumn()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/enterprise_checkout/update');
    }

    /**
     * Return current quote from regisrty
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function getQuote()
    {
        return Mage::registry('checkout_current_quote');
    }

    /**
     * Return current store from regisrty
     *
     * @return Mage_Core_Model_Store
     */
    protected function getStore()
    {
        return Mage::registry('checkout_current_store');
    }

    /**
     * Return current customer from regisrty
     *
     * @return Mage_Customer_Model_Customer
     */
    protected function getCustomer()
    {
        return Mage::registry('checkout_current_customer');
    }
}
