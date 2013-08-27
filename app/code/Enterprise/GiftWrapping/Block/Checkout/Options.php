<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping checkout process options block
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Block_Checkout_Options extends Magento_Core_Block_Template
{
    protected $_designCollection;

    protected $_giftWrappingAvailable = false;

    /**
     * Gift wrapping data
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Gift wrapping collection
     *
     * @return Enterprise_GiftWrapping_Model_Resource_Wrapping_Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = Mage::app()->getStore();
            $this->_designCollection = Mage::getModel('Enterprise_GiftWrapping_Model_Wrapping')->getCollection()
                ->addStoreAttributesToResult($store->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($store->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Select element for choosing gift wrapping design
     *
     * @return array
     */
    public function getDesignSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setData(array(
            'id'    => 'giftwrapping-${_id_}',
            'class' => 'select'
        ))
            ->setName('giftwrapping[${_id_}][design]')
            ->setExtraParams('data-addr-id="${_blockId_}"')
            ->setOptions($this->getDesignCollection()->toOptionArray());
        return $select->getHtml();
    }

    /**
     * Get quote instance
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('Magento_Checkout_Model_Session')->getQuote();
    }

    /**
     * Calculate including tax price
     *
     * @param Magento_Object $item
     * @param mixed $basePrice
     * @param Magento_Sales_Model_Quote_Address $shippingAddress
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $shippingAddress, $includeTax = false)
    {
        $billingAddress = $this->getQuote()->getBillingAddress();
        $taxClass = $this->_giftWrappingData->getWrappingTaxClass();
        $item->setTaxClassId($taxClass);

        $price = $this->_giftWrappingData->getPrice($item, $basePrice, $includeTax, $shippingAddress,
            $billingAddress
        );
        return $this->_coreData->currency($price, true, false);
    }

    /**
     * Return gift wrapping designs info
     *
     * @return Magento_Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            $temp = array();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->getQuote()->getIsMultiShipping() ? $address->getId() : $this->getQuote()->getId();
                if ($this->getDisplayWrappingBothPrices()) {
                    $temp[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address,
                        true
                    );
                    $temp[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address
                    );
                } else {
                    $temp[$entityId]['price'] = $this->calculatePrice(
                        $item,
                        $item->getBasePrice(),
                        $address,
                        $this->getDisplayWrappingIncludeTaxPrice()
                    );
                }
            }
            $temp['path'] = $item->getImageUrl();
            $data[$item->getId()] = $temp;
        }
        return new Magento_Object($data);
    }

    /**
     * Prepare and return quote items info
     *
     * @return Magento_Object
     */
    public function getItemsInfo()
    {
        $data = array();
        if ($this->getQuote()->getIsMultiShipping()) {
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $this->_processItems($address->getAllItems(), $address, $data);
            }
        } else {
            $this->_processItems($this->getQuote()->getAllItems(), $this->getQuote()->getShippingAddress(), $data);
        }
        return new Magento_Object($data);
    }

    /**
     * Process items
     *
     * @param array $items
     * @param Magento_Sales_Model_Quote_Address $shippingAddress
     * @param array $data
     * @return array
     */
    protected function _processItems($items, $shippingAddress, &$data)
    {
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allowed = $item->getProduct()->getGiftWrappingAvailable();
            if ($this->_giftWrappingData->isGiftWrappingAvailableForProduct($allowed)
                && !$item->getIsVirtual()) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(
                            new Magento_Object(),
                            $price,
                            $shippingAddress,
                            true
                        );
                        $temp['price_excl_tax'] = $this->calculatePrice(
                            new Magento_Object(),
                            $price,
                            $shippingAddress
                        );
                    } else {
                        $temp['price'] = $this->calculatePrice(
                            new Magento_Object(),
                            $price,
                            $shippingAddress,
                            $this->getDisplayWrappingIncludeTaxPrice()
                        );
                    }
                }
                $data[$item->getId()] = $temp;
            }
        }
        return $data;
    }

    /**
     * Prepare and return printed card info
     *
     * @return Magento_Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = $this->_giftWrappingData->getPrintedCardPrice();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->getQuote()->getIsMultiShipping()
                    ? $address->getId()
                    : $this->getQuote()->getId();

                if ($this->getDisplayCardBothPrices()) {
                    $data[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        new Magento_Object(),
                        $price,
                        $address,
                        true
                    );
                    $data[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        new Magento_Object(),
                        $price,
                        $address
                    );
                } else {
                    $data[$entityId]['price'] = $this->calculatePrice(
                        new Magento_Object(),
                        $price,
                        $address,
                        $this->getDisplayCardIncludeTaxPrice()
                    );
                }
            }
        }
        return new Magento_Object($data);
    }

    /**
     * Check display both prices for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return $this->_giftWrappingData->displayCartWrappingBothPrices();
    }

    /**
     * Check display both prices for printed card
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return $this->_giftWrappingData->displayCartCardBothPrices();
    }

    /**
     * Check display prices including tax for gift wrapping
     *
     * @return bool
     */
    public function getDisplayWrappingIncludeTaxPrice()
    {
        return $this->_giftWrappingData->displayCartWrappingIncludeTaxPrice();
    }

    /**
     * Check display price including tax for printed card
     *
     * @return bool
     */
    public function getDisplayCardIncludeTaxPrice()
    {
        return $this->_giftWrappingData->displayCartCardIncludeTaxPrice();
    }

    /**
     * Check allow printed card
     *
     * @return bool
     */
    public function getAllowPrintedCard()
    {
        return $this->_giftWrappingData->allowPrintedCard();
    }

    /**
     * Check allow gift receipt
     *
     * @return bool
     */
    public function getAllowGiftReceipt()
    {
        return $this->_giftWrappingData->allowGiftReceipt();
    }

    /**
     * Check allow gift wrapping on order level
     *
     * @return bool
     */
    public function getAllowForOrder()
    {
        return $this->_giftWrappingData->isGiftWrappingAvailableForOrder();
    }

    /**
     * Check allow gift wrapping on order items
     *
     * @return bool
     */
    public function getAllowForItems()
    {
        return $this->_giftWrappingData->isGiftWrappingAvailableForItems();
    }

    /**
     * Check allow gift wrapping for order
     *
     * @return bool
     */
    public function canDisplayGiftWrapping()
    {
        $cartItems      = Mage::getModel('Magento_Checkout_Model_Cart')->getItems();
        $productModel   = Mage::getModel('Magento_Catalog_Model_Product');
        foreach ($cartItems as $item) {
            $product = $productModel->load($item->getProductId());
            if ($product->getGiftWrappingAvailable()) {
                $this->_giftWrappingAvailable = true;
                continue;
            }
        }

        $canDisplay = $this->getAllowForOrder()
            || $this->getAllowForItems()
            || $this->getAllowPrintedCard()
            || $this->getAllowGiftReceipt()
            || $this->_giftWrappingAvailable;
        return $canDisplay;
    }

    /**
     * Determines if gift wrapping is available for any product in this checkout
     *
     * @return bool
     */
    public function getGiftWrappingAvailable()
    {
        return $this->_giftWrappingAvailable;
    }

    /**
     * Get design collection count
     *
     * @return int
     */
    public function getDesignCollectionCount()
    {
        return count($this->getDesignCollection());
    }
}
