<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TargetRule\Block\Checkout\Cart;

/**
 * TargetRule Checkout Cart Cross-Sell Products Block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Crosssell extends \Magento\TargetRule\Block\Product\AbstractProduct
{
    /**
     * Array of product objects in cart
     *
     * @var array
     */
    protected $_products;

    /**
     * Object of just added product to cart
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $_lastAddedProduct;

    /**
     * Whether get products by last added
     *
     * @var bool
     */
    protected $_byLastAddedProduct = false;

    /**
     * @var \Magento\TargetRule\Model\Index
     */
    protected $_index;

    /**
     * @var \Magento\TargetRule\Model\IndexFactory
     */
    protected $_indexFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_productLinkFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_status;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ProductTypes\ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\TargetRule\Model\Resource\Index $index
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\CatalogInventory\Model\Stock\Status $status
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\TargetRule\Model\IndexFactory $indexFactory
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\TargetRule\Model\Resource\Index $index,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\CatalogInventory\Model\Stock\Status $status,
        \Magento\Checkout\Model\Session $session,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\TargetRule\Model\IndexFactory $indexFactory,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        array $data = array()
    ) {
        $this->productTypeConfig = $productTypeConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_visibility = $visibility;
        $this->_status = $status;
        $this->_checkoutSession = $session;
        $this->_productLinkFactory = $productLinkFactory;
        $this->_productFactory = $productFactory;
        $this->_indexFactory = $indexFactory;
        parent::__construct(
            $context,
            $index,
            $targetRuleData,
            $data
        );
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return \Magento\TargetRule\Model\Rule::CROSS_SELLS;
    }

    /**
     * Retrieve just added to cart product id
     *
     * @return int|false
     */
    public function getLastAddedProductId()
    {
        return $this->_checkoutSession->getLastAddedProductId(true);
    }

    /**
     * Retrieve just added to cart product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getLastAddedProduct()
    {
        if (is_null($this->_lastAddedProduct)) {
            $productId = $this->getLastAddedProductId();
            if ($productId) {
                $this->_lastAddedProduct = $this->_productFactory->create()->load($productId);
            } else {
                $this->_lastAddedProduct = false;
            }
        }
        return $this->_lastAddedProduct;
    }

    /**
     * Retrieve quote instance
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
    }

    /**
     * Retrieve Array of Product instances in Cart
     *
     * @return array
     */
    protected function _getCartProducts()
    {
        if (is_null($this->_products)) {
            $this->_products = array();
            foreach ($this->getQuote()->getAllItems() as $quoteItem) {
                /* @var $quoteItem \Magento\Sales\Model\Quote\Item */
                $product = $quoteItem->getProduct();
                $this->_products[$product->getEntityId()] = $product;
            }
        }

        return $this->_products;
    }

    /**
     * Retrieve Array of product ids in Cart
     *
     * @return array
     */
    protected function _getCartProductIds()
    {
        $products = $this->_getCartProducts();
        return array_keys($products);
    }

    /**
     * Retrieve Array of product ids which have special relation with products in Cart
     * For example simple product as part of product type that represents product set
     *
     * @return array
     */
    protected function _getCartProductIdsRel()
    {
        $productIds = array();
        foreach ($this->getQuote()->getAllItems() as $quoteItem) {
            $productTypeOpt = $quoteItem->getOptionByCode('product_type');
            if ($productTypeOpt instanceof \Magento\Sales\Model\Quote\Item\Option &&
                $this->productTypeConfig->isProductSet(
                    $productTypeOpt->getValue()
                ) && $productTypeOpt->getProductId()
            ) {
                $productIds[] = $productTypeOpt->getProductId();
            }
        }

        return $productIds;
    }

    /**
     * Retrieve Target Rule Index instance
     *
     * @return \Magento\TargetRule\Model\Index
     */
    protected function _getTargetRuleIndex()
    {
        if (is_null($this->_index)) {
            $this->_index = $this->_indexFactory->create();
        }
        return $this->_index;
    }

    /**
     * Retrieve Maximum Number Of Product
     *
     * @return int
     */
    public function getPositionLimit()
    {
        return $this->_targetRuleData->getMaximumNumberOfProduct(\Magento\TargetRule\Model\Rule::CROSS_SELLS);
    }

    /**
     * Retrieve Position Behavior
     *
     * @return int
     */
    public function getPositionBehavior()
    {
        return $this->_targetRuleData->getShowProducts(\Magento\TargetRule\Model\Rule::CROSS_SELLS);
    }

    /**
     * Get link collection for cross-sell
     *
     * @throws \Magento\Framework\Model\Exception
     * @return \Magento\Catalog\Model\Resource\Product\Link\Product\Collection|null
     */
    protected function _getTargetLinkCollection()
    {
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Link\Product\Collection */
        $collection = $this->_productLinkFactory->create()->useCrossSellLinks()->getProductCollection()->setStoreId(
            $this->_storeManager->getStore()->getId()
        )->setGroupBy();
        $this->_addProductAttributesAndPrices($collection);
        $collection->setVisibility($this->_visibility->getVisibleInSiteIds());
        $this->_status->addIsInStockFilterToCollection($collection);

        return $collection;
    }

    /**
     * Retrieve array of cross-sell products for just added product to cart
     *
     * @return array
     */
    protected function _getProductsByLastAddedProduct()
    {
        $product = $this->getLastAddedProduct();
        if (!$product) {
            return array();
        }
        $this->_byLastAddedProduct = true;
        $items = parent::getItemCollection();
        $this->_byLastAddedProduct = false;
        $this->_items = null;
        return $items;
    }

    /**
     * Retrieve Product Ids from Cross-sell rules based products index by product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $count
     * @param array $excludeProductIds
     * @return array
     */
    protected function _getProductIdsFromIndexByProduct($product, $count, $excludeProductIds = array())
    {
        return $this->_getTargetRuleIndex()->setType(
            \Magento\TargetRule\Model\Rule::CROSS_SELLS
        )->setLimit(
            $count
        )->setProduct(
            $product
        )->setExcludeProductIds(
            $excludeProductIds
        )->getProductIds();
    }

    /**
     * Retrieve Product Collection by Product Ids
     *
     * @param array $productIds
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected function _getProductCollectionByIds($productIds)
    {
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        $this->_addProductAttributesAndPrices($collection);

        $collection->setVisibility($this->_visibility->getVisibleInCatalogIds());
        $this->_status->addIsInStockFilterToCollection($collection);

        return $collection;
    }

    /**
     * Retrieve Product Ids from Cross-sell rules based products index by products in shopping cart
     *
     * @param int $limit
     * @param array $excludeProductIds
     * @return array
     */
    protected function _getProductIdsFromIndexForCartProducts($limit, $excludeProductIds = array())
    {
        $resultIds = array();

        foreach ($this->_getCartProducts() as $product) {
            if ($product->getEntityId() == $this->getLastAddedProductId()) {
                continue;
            }

            $productIds = $this->_getProductIdsFromIndexByProduct(
                $product,
                $this->getPositionLimit(),
                $excludeProductIds
            );
            $resultIds = array_merge($resultIds, $productIds);
        }

        $resultIds = array_unique($resultIds);
        shuffle($resultIds);

        return array_slice($resultIds, 0, $limit);
    }

    /**
     * Get exclude product ids
     *
     * @return array
     */
    protected function _getExcludeProductIds()
    {
        $excludeProductIds = $this->_getCartProductIds();
        if (!is_null($this->_items)) {
            $excludeProductIds = array_merge(array_keys($this->_items), $excludeProductIds);
        }
        return $excludeProductIds;
    }

    /**
     * Get target rule based products for cross-sell
     *
     * @return array
     */
    protected function _getTargetRuleProducts()
    {
        $excludeProductIds = $this->_getExcludeProductIds();
        $limit = $this->getPositionLimit();
        $productIds = $this->_byLastAddedProduct ? $this->_getProductIdsFromIndexByProduct(
            $this->getLastAddedProduct(),
            $limit,
            $excludeProductIds
        ) : $this->_getProductIdsFromIndexForCartProducts(
            $limit,
            $excludeProductIds
        );

        $items = array();
        if ($productIds) {
            $collection = $this->_getProductCollectionByIds($productIds);
            foreach ($collection as $product) {
                $items[$product->getEntityId()] = $product;
            }
        }

        return $items;
    }

    /**
     * Get linked products
     *
     * @return array
     */
    protected function _getLinkProducts()
    {
        $items = array();
        $collection = $this->getLinkCollection();
        if ($collection) {
            if ($this->_byLastAddedProduct) {
                $collection->addProductFilter($this->getLastAddedProduct()->getEntityId());
            } else {
                $filterProductIds = array_merge($this->_getCartProductIds(), $this->_getCartProductIdsRel());
                $collection->addProductFilter($filterProductIds);
            }
            $collection->addExcludeProductFilter($this->_getExcludeProductIds());

            foreach ($collection as $product) {
                $items[$product->getEntityId()] = $product;
            }
        }
        return $items;
    }

    /**
     * Retrieve array of cross-sell products
     *
     * @return array
     */
    public function getItemCollection()
    {
        if (is_null($this->_items)) {
            // if has just added product to cart - load cross-sell products for it
            $productsByLastAdded = $this->_getProductsByLastAddedProduct();
            $limit = $this->getPositionLimit();
            if (count($productsByLastAdded) < $limit) {
                // reset collection
                $this->_linkCollection = null;
                parent::getItemCollection();
                // products by last added are preferable
                $this->_items = $productsByLastAdded + $this->_items;
                $this->_sliceItems();
            } else {
                $this->_items = $productsByLastAdded;
            }
            $this->_orderProductItems();
        }
        return $this->_items;
    }

    /**
     * Check is has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemsCount() > 0;
    }

    /**
     * Retrieve count of product in collection
     *
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->getItemCollection());
    }
}
