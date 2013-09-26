<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cart crosssell list
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Checkout_Block_Cart_Crosssell extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 4;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_productVisibility;

    /**
     * @var Magento_CatalogInventory_Model_Stock
     */
    protected $_stock;

    /**
     * @var Magento_Catalog_Model_Product_LinkFactory
     */
    protected $_productLinkFactory;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Product_Visibility $productVisibility
     * @param Magento_CatalogInventory_Model_Stock $stock
     * @param Magento_Catalog_Model_Product_LinkFactory $productLinkFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Product_Visibility $productVisibility,
        Magento_CatalogInventory_Model_Stock $stock,
        Magento_Catalog_Model_Product_LinkFactory $productLinkFactory,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_productVisibility = $productVisibility;
        $this->_stock = $stock;
        $this->_productLinkFactory = $productLinkFactory;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Get crosssell items
     *
     * @return array
     */
    public function getItems()
    {
        $items = $this->getData('items');
        if (is_null($items)) {
            $items = array();
            $ninProductIds = $this->_getCartProductIds();
            if ($ninProductIds) {
                $lastAdded = (int) $this->_getLastAddedProductId();
                if ($lastAdded) {
                    $collection = $this->_getCollection()
                        ->addProductFilter($lastAdded);
                    if (!empty($ninProductIds)) {
                        $collection->addExcludeProductFilter($ninProductIds);
                    }
                    $collection->setPositionOrder()->load();

                    foreach ($collection as $item) {
                        $ninProductIds[] = $item->getId();
                        $items[] = $item;
                    }
                }

                if (count($items) < $this->_maxItemCount) {
                    $filterProductIds = array_merge($this->_getCartProductIds(), $this->_getCartProductIdsRel());
                    $collection = $this->_getCollection()
                        ->addProductFilter($filterProductIds)
                        ->addExcludeProductFilter($ninProductIds)
                        ->setPageSize($this->_maxItemCount-count($items))
                        ->setGroupBy()
                        ->setPositionOrder()
                        ->load();
                    foreach ($collection as $item) {
                        $items[] = $item;
                    }
                }

            }

            $this->setData('items', $items);
        }
        return $items;
    }

    /**
     * Count items
     *
     * @return int
     */
    public function getItemCount()
    {
        return count($this->getItems());
    }

    /**
     * Get ids of products that are in cart
     *
     * @return array
     */
    protected function _getCartProductIds()
    {
        $ids = $this->getData('_cart_product_ids');
        if (is_null($ids)) {
            $ids = array();
            foreach ($this->getQuote()->getAllItems() as $item) {
                $product = $item->getProduct();
                if ($product) {
                    $ids[] = $product->getId();
                }
            }
            $this->setData('_cart_product_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve Array of product ids which have special relation with products in Cart
     * For example simple product as part of Grouped product
     *
     * @return array
     */
    protected function _getCartProductIdsRel()
    {
        $productIds = array();
        foreach ($this->getQuote()->getAllItems() as $quoteItem) {
            $productTypeOpt = $quoteItem->getOptionByCode('product_type');
            if ($productTypeOpt instanceof Magento_Sales_Model_Quote_Item_Option
                && $productTypeOpt->getValue() == Magento_Catalog_Model_Product_Type_Grouped::TYPE_CODE
                && $productTypeOpt->getProductId()
            ) {
                $productIds[] = $productTypeOpt->getProductId();
            }
        }

        return $productIds;
    }

    /**
     * Get last product ID that was added to cart and remove this information from session
     *
     * @return int
     */
    protected function _getLastAddedProductId()
    {
        return $this->_checkoutSession->getLastAddedProductId(true);
    }

    /**
     * Get quote instance
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Get crosssell products collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Link_Product_Collection
     */
    protected function _getCollection()
    {
        /** @var Magento_Catalog_Model_Resource_Product_Link_Product_Collection $collection */
        $collection = $this->_productLinkFactory->create()->useCrossSellLinks()
            ->getProductCollection()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->addStoreFilter()
            ->setPageSize($this->_maxItemCount)
            ->setVisibility($this->_productVisibility->getVisibleInCatalogIds());
        $this->_addProductAttributesAndPrices($collection);

        $this->_stock->addInStockFilterToCollection($collection);

        return $collection;
    }
}
