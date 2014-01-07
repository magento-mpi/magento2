<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

/**
 * Catalog Product Compare Helper
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Compare extends \Magento\Core\Helper\Url
{
    /**
     * Product Compare Items Collection
     *
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection
     */
    protected $_itemCollection;

    /**
     * Product Comapare Items Collection has items flag
     *
     * @var bool
     */
    protected $_hasItems;

    /**
     * Allow used Flat catalog product for product compare items collection
     *
     * @var bool
     */
    protected $_allowUsedFlat = true;

    /**
     * Customer id
     *
     * @var null|int
     */
    protected $_customerId = null;

    /**
     * Catalog session
     *
     * @var \Magento\Catalog\Model\Session
     */
    protected $_catalogSession;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Log visitor
     *
     * @var \Magento\Log\Model\Visitor
     */
    protected $_logVisitor;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product compare item collection factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * @var \Magento\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $itemCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Magento\Log\Model\Visitor $logVisitor
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Magento\Data\Form\FormKey $formKey
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Log\Model\Visitor $logVisitor,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Data\Form\FormKey $formKey
    ) {
        $this->_itemCollectionFactory = $itemCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_logVisitor = $logVisitor;
        $this->_customerSession = $customerSession;
        $this->_catalogSession = $catalogSession;
        $this->_formKey = $formKey;
        parent::__construct($context, $storeManager);
    }

    /**
     * Retrieve compare list url
     *
     * @return string
     */
    public function getListUrl()
    {
        $itemIds = array();
        foreach ($this->getItemCollection() as $item) {
            $itemIds[] = $item->getId();
        }

        $params = array(
            'items'=>implode(',', $itemIds),
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );

        return $this->_getUrl('catalog/product_compare', $params);
    }

    /**
     * Get parameters used for build add product to compare list urls
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  array
     */
    protected function _getUrlParams($product)
    {
        return array(
            'product' => $product->getId(),
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl(),
            'form_key' => $this->_formKey->getFormKey(),
        );
    }

    /**
     * Retrieve url for adding product to conpare list
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  string
     */
    public function getAddUrl($product)
    {
        return $this->_getUrl('catalog/product_compare/add', $this->_getUrlParams($product));
    }

    /**
     * Retrieve add to wishlist url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();

        $params = array(
            'product'=>$product->getId(),
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrieve add to cart url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        $beforeCompareUrl = $this->_catalogSession->getBeforeCompareUrl();
        $params = array(
            'product'=>$product->getId(),
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('checkout/cart/add', $params);
    }

    /**
     * Retrieve remove item from compare list url
     *
     * @param   $item
     * @return  string
     */
    public function getRemoveUrl($item)
    {
        $params = array(
            'product'=>$item->getId(),
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('catalog/product_compare/remove', $params);
    }

    /**
     * Retrieve clear compare list url
     *
     * @return string
     */
    public function getClearListUrl()
    {
        $params = array(
            \Magento\App\Action\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
        );
        return $this->_getUrl('catalog/product_compare/clear', $params);
    }

    /**
     * Retrieve compare list items collection
     *
     * @return \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection
     */
    public function getItemCollection()
    {
        if (!$this->_itemCollection) {
            // cannot be placed in constructor because of the cyclic dependency which cannot be fixed with proxy class
            // collection uses this helper in constructor when calling isEnabledFlat() method
            $this->_itemCollection = $this->_itemCollectionFactory->create();
            $this->_itemCollection->useProductItem(true)
                ->setStoreId($this->_storeManager->getStore()->getId());

            if ($this->_customerSession->isLoggedIn()) {
                $this->_itemCollection->setCustomerId($this->_customerSession->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_itemCollection->setCustomerId($this->_customerId);
            } else {
                $this->_itemCollection->setVisitorId($this->_logVisitor->getId());
            }

            $this->_itemCollection->setVisibility(
                $this->_catalogProductVisibility->getVisibleInSiteIds()
            );

            /* Price data is added to consider item stock status using price index */
            $this->_itemCollection->addPriceData();

            $this->_itemCollection->addAttributeToSelect('name')
                ->addUrlRewrite()
                ->load();

            /* update compare items count */
            $this->_catalogSession->setCatalogCompareItemsCount(count($this->_itemCollection));
        }

        return $this->_itemCollection;
    }

    /**
     * Calculate cache product compare collection
     *
     * @param  bool $logout
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    public function calculate($logout = false)
    {
        // first visit
        if (!$this->_catalogSession->hasCatalogCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            /** @var $collection \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection */
            $collection = $this->_itemCollectionFactory->create()
                ->useProductItem(true);
            if (!$logout && $this->_customerSession->isLoggedIn()) {
                $collection->setCustomerId($this->_customerSession->getCustomerId());
            } elseif ($this->_customerId) {
                $collection->setCustomerId($this->_customerId);
            } else {
                $collection->setVisitorId($this->_logVisitor->getId());
            }

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData()
                ->setVisibility($this->_catalogProductVisibility->getVisibleInSiteIds());

            $count = $collection->getSize();
        }

        $this->_catalogSession->setCatalogCompareItemsCount($count);

        return $this;
    }

    /**
     * Retrieve count of items in compare list
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_catalogSession->hasCatalogCompareItemsCount()) {
            $this->calculate();
        }

        return $this->_catalogSession->getCatalogCompareItemsCount();
    }

    /**
     * Check has items
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Set is allow used flat (for collection)
     *
     * @param bool $flag
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    public function setAllowUsedFlat($flag)
    {
        $this->_allowUsedFlat = (bool)$flag;
        return $this;
    }

    /**
     * Retrieve is allow used flat (for collection)
     *
     * @return bool
     */
    public function getAllowUsedFlat()
    {
        return $this->_allowUsedFlat;
    }

    /**
     * Setter for customer id
     *
     * @param int $id
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    public function setCustomerId($id)
    {
        $this->_customerId = $id;
        return $this;
    }
}
