<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart items grid
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Items extends Magento_Adminhtml_Block_Template
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
        $this->_wishlistFactory = $wishlistFactory;
    }

    /**
     * Rterieve grid id in template
     *
     * @return string
     */
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
        $res = Mage::getSingleton('Magento_Tax_Model_Config')->displayCartSubtotalInclTax($this->getStore())
            || Mage::getSingleton('Magento_Tax_Model_Config')->displayCartSubtotalBoth($this->getStore());

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
        } else {
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

    /**
     * Check whether to use custom price for item
     *
     * @param $item
     * @return bool
     */
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
        return $this->_authorization->isAllowed('Magento_AdvancedCheckout::update');
    }

    /**
     * Return current quote from registry
     *
     * @return Magento_Sales_Model_Quote
     */
    protected function getQuote()
    {
        return $this->_registry->registry('checkout_current_quote');
    }

    /**
     * Return current store from registry
     *
     * @return Magento_Core_Model_Store
     */
    protected function getStore()
    {
        return $this->_registry->registry('checkout_current_store');
    }

    /**
     * Return current customer from registry
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function getCustomer()
    {
        return $this->_registry->registry('checkout_current_customer');
    }

    /**
     * Generate configure button html
     *
     * @param  Magento_Sales_Model_Quote_Item $item
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
        return sprintf(
            '<button type="button" class="scalable %s" %s><span><span><span>%s</span></span></span></button>',
            $class,
            $addAttributes,
            __('Configure')
        );
    }

    /**
     * Returns whether moving to wishlist is allowed for this item
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return bool
     */
    public function isMoveToWishlistAllowed($item)
    {
        return $item->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Retrieve collection of customer wishlists
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getCustomerWishlists()
    {
        /* @var Magento_Wishlist_Model_Resource_Wishlist_Collection $wishlistCollection */
        return $this->_wishlistFactory->create()->getCollection()
            ->filterByCustomerId($this->getCustomerId());
    }
}
