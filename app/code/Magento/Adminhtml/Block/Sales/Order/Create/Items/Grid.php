<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml sales order create items grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Items_Grid extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Flag to check can items be move to customer storage
     *
     * @var bool
     */
    protected $_moveToCustomerStorage = true;

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var Magento_Adminhtml_Model_Giftmessage_Save
     */
    protected $_giftMessageSave;

    /**
     * @var Magento_Tax_Model_Config
     */
    protected $_taxConfig;

    /**
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param Magento_Adminhtml_Model_Giftmessage_Save $giftMessageSave
     * @param Magento_Tax_Model_Config $taxConfig
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        Magento_Adminhtml_Model_Giftmessage_Save $giftMessageSave,
        Magento_Tax_Model_Config $taxConfig,
        Magento_Tax_Helper_Data $taxData,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistFactory = $wishlistFactory;
        $this->_giftMessageSave = $giftMessageSave;
        $this->_taxConfig = $taxConfig;
        $this->_taxData = $taxData;
        parent::__construct($sessionQuote, $orderCreate, $coreData, $context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_search_grid');
    }

    public function getItems()
    {
        $items = $this->getParentBlock()->getItems();
        $oldSuperMode = $this->getQuote()->getIsSuperMode();
        $this->getQuote()->setIsSuperMode(false);
        foreach ($items as $item) {
            // To dispatch inventory event sales_quote_item_qty_set_after, set item qty
            $item->setQty($item->getQty());
            $stockItem = $item->getProduct()->getStockItem();
            if ($stockItem instanceof Magento_CatalogInventory_Model_Stock_Item) {
                // This check has been performed properly in Inventory observer, so it has no sense
                /*
                $check = $stockItem->checkQuoteItemQty($item->getQty(), $item->getQty(), $item->getQty());
                $item->setMessage($check->getMessage());
                $item->setHasError($check->getHasError());
                */
                if ($item->getProduct()->getStatus() == Magento_Catalog_Model_Product_Status::STATUS_DISABLED) {
                    $item->setMessage(__('This product is disabled.'));
                    $item->setHasError(true);
                }
            }
        }
        $this->getQuote()->setIsSuperMode($oldSuperMode);
        return $items;
    }

    public function getSession()
    {
        return $this->getParentBlock()->getSession();
    }

    public function getItemEditablePrice($item)
    {
        return $item->getCalculationPrice()*1;
    }

    public function getOriginalEditablePrice($item)
    {
        if ($item->hasOriginalCustomPrice()) {
            $result = $item->getOriginalCustomPrice()*1;
        } elseif ($item->hasCustomPrice()) {
            $result = $item->getCustomPrice()*1;
        } else {
            if ($this->_taxData->priceIncludesTax($this->getStore())) {
                $result = $item->getPriceInclTax()*1;
            } else {
                $result = $item->getOriginalPrice()*1;
            }
        }
        return $result;
    }

    public function getItemOrigPrice($item)
    {
//        return $this->convertPrice($item->getProduct()->getPrice());
        return $this->convertPrice($item->getPrice());
    }

    public function isGiftMessagesAvailable($item=null)
    {
        if(is_null($item)) {
            return $this->helper('Magento_GiftMessage_Helper_Message')->getIsMessagesAvailable(
                'items', $this->getQuote(), $this->getStore()
            );
        }

        return $this->helper('Magento_GiftMessage_Helper_Message')->getIsMessagesAvailable(
            'item', $item, $this->getStore()
        );
    }

    public function isAllowedForGiftMessage($item)
    {
        return $this->_giftMessageSave->getIsAllowedQuoteItem($item);
    }

    /**
     * Check if we need display grid totals include tax
     *
     * @return bool
     */
    public function displayTotalsIncludeTax()
    {
        $res = $this->_taxConfig->displayCartSubtotalInclTax($this->getStore())
            || $this->_taxConfig->displayCartSubtotalBoth($this->getStore());
        return $res;
    }

    public function getSubtotal()
    {
        $address = $this->getQuoteAddress();
        if ($this->displayTotalsIncludeTax()) {
            if ($address->getSubtotalInclTax()) {
                return $address->getSubtotalInclTax();
            }
            return $address->getSubtotal()+$address->getTaxAmount();
        } else {
            return $address->getSubtotal();
        }
        return false;
    }

    public function getSubtotalWithDiscount()
    {
        $address = $this->getQuoteAddress();
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
     * Retrive quote address
     *
     * @return Magento_Sales_Model_Quote_Address
     */
    public function getQuoteAddress()
    {
        if ($this->getQuote()->isVirtual()) {
            return $this->getQuote()->getBillingAddress();
        }
        else {
            return $this->getQuote()->getShippingAddress();
        }
    }

    /**
     * Define if specified item has already applied custom price
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return bool
     */
    public function usedCustomPriceForItem($item)
    {
        return $item->hasCustomPrice();
    }

    /**
     * Define if custom price can be applied for specified item
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return bool
     */
    public function canApplyCustomPrice($item)
    {
        return !$item->isChildrenCalculated();
    }

    public function getQtyTitle($item)
    {
        $prices = $item->getProduct()->getTierPrice();
        if ($prices) {
            $info = array();
            foreach ($prices as $data) {
                $qty    = $data['price_qty']*1;
                $price  = $this->convertPrice($data['price']);
                $info[] = __('Buy %1 for price %2', $qty, $price);
            }
            return implode(', ', $info);
        }
        else {
            return __('Item ordered qty');
        }
    }

    /**
     * Get tier price html
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getTierHtml($item)
    {
        $html = '';
        $prices = $item->getProduct()->getTierPrice();
        if ($prices) {
            $info = $item->getProductType() == Magento_Catalog_Model_Product_Type::TYPE_BUNDLE
                ? $this->_getBundleTierPriceInfo($prices)
                : $this->_getTierPriceInfo($prices);
            $html = implode('<br/>', $info);
        }
        return $html;
    }

    /**
     * Get tier price info to display in grid for Bundle product
     *
     * @param array $prices
     * @return array
     */
    protected function _getBundleTierPriceInfo($prices)
    {
        $info = array();
        foreach ($prices as $data) {
            $qty    = $data['price_qty'] * 1;
            $info[] = __('%1 with %2 discount each', $qty, ($data['price'] * 1) . '%');
        }
        return $info;
    }

    /**
     * Get tier price info to display in grid
     *
     * @param array $prices
     * @return array
     */
    protected function _getTierPriceInfo($prices)
    {
        $info = array();
        foreach ($prices as $data) {
            $qty    = $data['price_qty'] * 1;
            $price  = $this->convertPrice($data['price']);
            $info[] = __('%1 for %2', $qty, $price);
        }
        return $info;
    }
    /**
     * Get Custom Options of item
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return array
     */
    public function getCustomOptions(Magento_Sales_Model_Quote_Item $item)
    {
        $optionStr = '';
        $this->_moveToCustomerStorage = true;
        if ($optionIds = $item->getOptionByCode('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $item->getProduct()->getOptionById($optionId)) {
                    $optionValue = $item->getOptionByCode('option_' . $option->getId())->getValue();

                    $optionStr .= $option->getTitle() . ':';

                    $quoteItemOption = $item->getOptionByCode('option_' . $option->getId());
                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setQuoteItemOption($quoteItemOption);

                    $optionStr .= $group->getEditableOptionValue($quoteItemOption->getValue());
                    $optionStr .= "\n";
                }
            }
        }
        return $optionStr;
    }

    /**
     * Get flag for rights to move items to customer storage
     *
     * @return bool
     */
    public function getMoveToCustomerStorage()
    {
        return $this->_moveToCustomerStorage;
    }

    public function displaySubtotalInclTax($item)
    {
        if ($item->getTaxBeforeDiscount()) {
            $tax = $item->getTaxBeforeDiscount();
        } else {
            $tax = $item->getTaxAmount() ? $item->getTaxAmount() : 0;
        }
        return $this->formatPrice($item->getRowTotal() + $tax);
    }

    public function displayOriginalPriceInclTax($item)
    {
        $tax = 0;
        if ($item->getTaxPercent()) {
            $tax = $item->getPrice() * ($item->getTaxPercent() / 100);
        }
        return $this->convertPrice($item->getPrice()+($tax/$item->getQty()));
    }

    public function displayRowTotalWithDiscountInclTax($item)
    {
        $tax = ($item->getTaxAmount() ? $item->getTaxAmount() : 0);
        return $this->formatPrice($item->getRowTotal()-$item->getDiscountAmount()+$tax);
    }

    public function getInclExclTaxMessage()
    {
        if ($this->_taxData->priceIncludesTax($this->getStore())) {
            return __('* - Enter custom price including tax');
        } else {
            return __('* - Enter custom price excluding tax');
        }
    }

    public function getStore()
    {
        return $this->getQuote()->getStore();
    }

    /**
     * Return html button which calls configure window
     *
     * @param  $item
     * @return string
     */
    public function getConfigureButtonHtml($item)
    {
        $product = $item->getProduct();

        $options = array('label' => __('Configure'));
        if ($product->canConfigure()) {
            $options['onclick'] = sprintf('order.showQuoteItemConfiguration(%s)', $item->getId());
        } else {
            $options['class'] = ' disabled';
            $options['title'] = __('This product does not have any configurable options');
        }

        return $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData($options)
            ->toHtml();
    }

    /**
     * Get order item extra info block
     *
     * @param Magento_Sales_Model_Quote_Item $item
     * @return Magento_Core_Block_Abstract
     */
    public function getItemExtraInfo($item)
    {
        return $this->getLayout()
            ->getBlock('order_item_extra_info')
            ->setItem($item);
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
        return $this->_wishlistFactory->create()->getCollection()
            ->filterByCustomerId($this->getCustomerId());
    }
}
