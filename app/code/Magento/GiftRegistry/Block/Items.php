<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Block;

/**
 * Front end helper block to show giftregistry items
 */
class Items extends \Magento\Checkout\Block\Cart
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrlBuilder
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Sales\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Resource\Url $catalogUrlBuilder,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_cartHelper = $cartHelper;
        $this->_coreRegistry = $registry;
        $this->itemFactory = $itemFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $catalogUrlBuilder,
            $cartHelper,
            $httpContext,
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
