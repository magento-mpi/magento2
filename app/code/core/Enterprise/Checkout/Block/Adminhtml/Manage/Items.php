<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart items grid
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Checkout_Block_Adminhtml_Manage_Items extends Mage_Adminhtml_Block_Template
{
    public function getJsObjectName()
    {
        return 'checkoutItemsGrid';
    }

    /**
     * Prepare items collection
     *
     * @return array
     */
    protected function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }

    /**
     * Return current customer id
     *
     * @return int
     */
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
        $res = Mage::getSingleton('Mage_Tax_Model_Config')->displayCartSubtotalInclTax($this->getStore())
            || Mage::getSingleton('Mage_Tax_Model_Config')->displayCartSubtotalBoth($this->getStore());

        return $res;
    }

    /**
     * Return quote subtotal
     *
     * @return float|bool
     */
    public function getSubtotal()
    {
        if ($this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getBillingAddress();
        }
        else {
            $address = $this->getQuote()->getShippingAddress();
        }
        if ($this->displayTotalsIncludeTax()) {
            return $address->getSubtotal() + $address->getTaxAmount();
        } else {
            return $address->getSubtotal();
        }

        return false;
    }

    /**
     * Return quote subtotal with discount applied
     *
     * @return float
     */
    public function getSubtotalWithDiscount()
    {
        $address = $this->getQuote()->getShippingAddress();
        if ($this->displayTotalsIncludeTax()) {
            return $address->getSubtotal() + $address->getTaxAmount() + $this->getDiscountAmount();
        } else {
            return $address->getSubtotal() + $this->getDiscountAmount();
        }
    }

    /**
     * Return quote discount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getQuote()->getShippingAddress()->getDiscountAmount();
    }

    /**
     * Return formatted price
     *
     * @param decimal $value
     * @return string
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
     * ACL limitations
     *
     * @return bool
     */
    public function isAllowedActionColumn()
    {
        return Mage::getSingleton('Mage_Admin_Model_Session')->isAllowed('sales/enterprise_checkout/update');
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

    /**
     * Generate configure button html
     *
     * @param  Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();
        if ($product->canConfigure()) {
            $class = '';
            $addAttributes = sprintf('onclick="checkoutObj.showQuoteItemConfiguration(%s)"', $item->getId());
        } else {
            $class = 'disabled';
            $addAttributes = 'disabled="disabled"';
        }
        return sprintf('<button type="button" class="scalable %s" %s><span>%s</span></button>',
            $class, $addAttributes, Mage::helper('Mage_Sales_Helper_Data')->__('Configure'));
    }

    /**
     * Returns whether moving to wishlist is allowed for this item
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return bool
     */
    public function isMoveToWishlistAllowed($item)
    {
        return $item->getProduct()->isVisibleInSiteVisibility();
    }
}
