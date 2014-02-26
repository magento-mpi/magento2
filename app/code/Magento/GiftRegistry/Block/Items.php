<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block;

use Magento\Customer\Service\V1\CustomerServiceInterface as CustomerService;

/**
 * Front end helper block to show giftregistry items
 */
class Items extends \Magento\Checkout\Block\Cart
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Tax data
     *
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxData = null;

    /**
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Sales\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param CustomerService $customerService
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Resource\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        CustomerService $customerService,
        array $data = array()
    ) {
        $this->_cartHelper = $cartHelper;
        $this->_taxData = $taxData;
        $this->_coreRegistry = $registry;
        $this->itemFactory = $itemFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        parent::__construct(
            $context,
            $catalogData,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $customerService,
            $data
        );
    }

    /**
     * Return list of gift registry items
     *
     * @return array
     */
    public function getItems()
    {
        if (!$this->hasItemCollection()) {
            if (!$this->getEntity()) {
                return array();
            }
            $collection = $this->itemFactory->create()->getCollection()
                ->addRegistryFilter($this->getEntity()->getId())
                ->addWebsiteFilter();

            $quoteItemsCollection = array();
            $quote = $this->quoteFactory->create()->setItemCount(true);
            $emptyQuoteItem = $this->quoteItemFactory->create();
            foreach ($collection as $item) {
                $product = $item->getProduct();
                $remainingQty = $item->getQty() - $item->getQtyFulfilled();
                if ($remainingQty < 0) {
                    $remainingQty = 0;
                }
                // Create a new qoute item and import data from gift registry item to it
                $quoteItem = clone $emptyQuoteItem;
                $quoteItem->addData($item->getData())
                    ->setQuote($quote)
                    ->setProduct($product)
                    ->setRemainingQty($remainingQty)
                    ->setOptions($item->getOptions());

                $product->setCustomOptions($item->getOptionsByCode());
                if ($this->_catalogData->canApplyMsrp($product)) {
                    $quoteItem->setCanApplyMsrp(true);
                    $product->setRealPriceHtml(
                        $this->_storeManager->getStore()->formatPrice($this->_storeManager->getStore()->convertPrice(
                            $this->_taxData->getPrice($product, $product->getFinalPrice(), true)
                        ))
                    );
                    $product->setAddToCartUrl($this->_cartHelper->getAddUrl($product));
                } else {
                    $quoteItem->setGiftRegistryPrice($product->getFinalPrice());
                    $quoteItem->setCanApplyMsrp(false);
                }

                $quoteItemsCollection[] = $quoteItem;
            }

            $this->setData('item_collection', $quoteItemsCollection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Return current gift registry entity
     *
     * @return \Magento\GiftRegistry\Model\Entity
     */
    public function getEntity()
    {
        if (!$this->hasEntity()) {
            $this->setData('entity', $this->_coreRegistry->registry('current_entity'));
        }
        return $this->_getData('entity');
    }

    /**
     * Return "add to cart" url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/addToCart', array('_current' => true));
    }

    /**
     * Return update action form url
     *
     * @return string
     */
    public function getActionUpdateUrl()
    {
        return $this->getUrl('*/*/updateItems', array('_current' => true));
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }
}
