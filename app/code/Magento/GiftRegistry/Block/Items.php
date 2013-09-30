<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to show giftregistry items
 */
class Magento_GiftRegistry_Block_Items extends Magento_Checkout_Block_Cart
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Tax data
     *
     * @var Magento_Tax_Helper_Data
     */
    protected $_taxData = null;

    /**
     * @var Magento_GiftRegistry_Model_ItemFactory
     */
    protected $itemFactory;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var Magento_Sales_Model_Quote_ItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Catalog_Model_Resource_Url $catalogUrlBuilder
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_GiftRegistry_Model_ItemFactory $itemFactory
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Tax_Helper_Data $taxData
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Catalog_Model_Resource_Url $catalogUrlBuilder,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_GiftRegistry_Model_ItemFactory $itemFactory,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Sales_Model_Quote_ItemFactory $quoteItemFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Registry $registry,
        Magento_Tax_Helper_Data $taxData,
        array $data = array()
    ) {
        $this->_taxData = $taxData;
        $this->_coreRegistry = $registry;
        $this->itemFactory = $itemFactory;
        $this->quoteFactory = $quoteFactory;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->storeManager = $storeManager;
        parent::__construct($catalogData, $coreData, $context, $customerSession, $checkoutSession, $storeManager,
            $catalogUrlBuilder, $urlBuilder, $data);
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
                ->addRegistryFilter($this->getEntity()->getId());

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
                        $this->storeManager->getStore()->formatPrice($this->storeManager->getStore()->convertPrice(
                            $this->_taxData->getPrice($product, $product->getFinalPrice(), true)
                        ))
                    );
                    $product->setAddToCartUrl($this->helper('Magento_Checkout_Helper_Cart')->getAddUrl($product));
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
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
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
