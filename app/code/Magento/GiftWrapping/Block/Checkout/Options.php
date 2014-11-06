<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Block\Checkout;

/**
 * Gift wrapping checkout process options block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Options extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
     */
    protected $_designCollection;

    /**
     * @var bool
     */
    protected $_giftWrappingAvailable = false;

    /**
     * Gift wrapping data
     *
     * @var \Magento\GiftWrapping\Helper\Data
     */
    protected $_giftWrappingData = null;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_checkoutCartFactory;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory
     */
    protected $_wrappingCollectionFactory;

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * Checkout types for order level and item level
     *
     * @var array
     */
    protected $checkoutItems;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\GiftWrapping\Helper\Data $giftWrappingData
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\CartFactory $checkoutCartFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param array $checkoutItems
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\GiftWrapping\Helper\Data $giftWrappingData,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\CartFactory $checkoutCartFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $checkoutItems,
        array $data = array()
    ) {
        $this->_coreData = $coreData;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_wrappingCollectionFactory = $wrappingCollectionFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_checkoutCartFactory = $checkoutCartFactory;
        $this->productRepository = $productRepository;
        $this->checkoutItems = $checkoutItems;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Gift wrapping collection
     *
     * @return \Magento\GiftWrapping\Model\Resource\Wrapping\Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = $this->_storeManager->getStore();
            $this->_designCollection = $this->_wrappingCollectionFactory->create()->addStoreAttributesToResult(
                $store->getId()
            )->applyStatusFilter()->applyWebsiteFilter(
                $store->getWebsiteId()
            );
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
        $select = $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Select'
        )->setData(
            array('id' => 'giftwrapping-${_type_}-${_id_}', 'class' => 'select')
        )->setName(
            'giftwrapping[${_type_}][${_id_}][design]'
        )->setExtraParams(
            'data-addr-id="${_blockId_}"'
        )->setOptions(
            $this->getDesignCollection()->toOptionArray()
        );
        return $select->getHtml();
    }

    /**
     * Get quote instance
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Calculate including tax price
     *
     * @param \Magento\Framework\Object $item
     * @param float $basePrice
     * @param \Magento\Sales\Model\Quote\Address $shippingAddress
     * @param bool $includeTax
     * @return string
     */
    public function calculatePrice($item, $basePrice, $shippingAddress, $includeTax = false)
    {
        $billingAddress = $this->getQuote()->getBillingAddress();
        $taxClass = $this->_giftWrappingData->getWrappingTaxClass();
        $item->setTaxClassId($taxClass);

        $price = $this->_giftWrappingData->getPrice($item, $basePrice, $includeTax, $shippingAddress, $billingAddress);
        return $this->_coreData->currency($price, true, false);
    }

    /**
     * Return gift wrapping designs info
     *
     * @return \Magento\Framework\Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        /** @var $item \Magento\GiftWrapping\Model\Wrapping */
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
                    $temp[$entityId]['price_excl_tax'] = $this->calculatePrice($item, $item->getBasePrice(), $address);
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
        return new \Magento\Framework\Object($data);
    }

    /**
     * Prepare and return quote items info
     *
     * @return \Magento\Framework\Object
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
        return new \Magento\Framework\Object($data);
    }

    /**
     * Provide variable name for input items
     *
     * @param $level string; 'order_level' and 'item_level' are expected
     * @throws \InvalidArgumentException
     * @return string|null
     */
    public function getCheckoutTypeVariable($level)
    {
        $checkoutType = $this->getQuote()->getIsMultiShipping() ? 'multishipping' : 'onepage';
        if (array_key_exists($level, $this->checkoutItems[$checkoutType])) {
            return $this->checkoutItems[$checkoutType][$level];
        }

        throw new \InvalidArgumentException('Invalid level: ' . $level);
    }

    /**
     * Process items
     *
     * @param array $items
     * @param \Magento\Sales\Model\Quote\Address $shippingAddress
     * @param array &$data
     * @return array
     */
    protected function _processItems($items, $shippingAddress, &$data)
    {
        /** @var $item \Magento\Sales\Model\Quote\Item */
        foreach ($items as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $allowed = $item->getProduct()->getGiftWrappingAvailable();
            if ($this->_giftWrappingData->isGiftWrappingAvailableForProduct($allowed) && !$item->getIsVirtual()) {
                $temp = array();
                if ($price = $item->getProduct()->getGiftWrappingPrice()) {
                    if ($this->getDisplayWrappingBothPrices()) {
                        $temp['price_incl_tax'] = $this->calculatePrice(
                            new \Magento\Framework\Object(),
                            $price,
                            $shippingAddress,
                            true
                        );
                        $temp['price_excl_tax'] = $this->calculatePrice(
                            new \Magento\Framework\Object(),
                            $price,
                            $shippingAddress
                        );
                    } else {
                        $temp['price'] = $this->calculatePrice(
                            new \Magento\Framework\Object(),
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
     * @return \Magento\Framework\Object
     */
    public function getCardInfo()
    {
        $data = array();
        if ($this->getAllowPrintedCard()) {
            $price = $this->_giftWrappingData->getPrintedCardPrice();
            foreach ($this->getQuote()->getAllShippingAddresses() as $address) {
                $entityId = $this->getQuote()->getIsMultiShipping() ? $address->getId() : $this->getQuote()->getId();

                if ($this->getDisplayCardBothPrices()) {
                    $data[$entityId]['price_incl_tax'] = $this->calculatePrice(
                        new \Magento\Framework\Object(),
                        $price,
                        $address,
                        true
                    );
                    $data[$entityId]['price_excl_tax'] = $this->calculatePrice(
                        new \Magento\Framework\Object(),
                        $price,
                        $address
                    );
                } else {
                    $data[$entityId]['price'] = $this->calculatePrice(
                        new \Magento\Framework\Object(),
                        $price,
                        $address,
                        $this->getDisplayCardIncludeTaxPrice()
                    );
                }
            }
        }
        return new \Magento\Framework\Object($data);
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
        $cartItems = $this->_checkoutCartFactory->create()->getItems();
        foreach ($cartItems as $item) {
            $product = $this->productRepository->getById($item->getProductId());
            if ($product->getGiftWrappingAvailable()) {
                $this->_giftWrappingAvailable = true;
                continue;
            }
        }

        $canDisplay = $this->getAllowForOrder() ||
            $this->getAllowForItems() ||
            $this->getAllowPrintedCard() ||
            $this->getAllowGiftReceipt() ||
            $this->_giftWrappingAvailable;
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
