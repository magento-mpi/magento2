<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Product Compare Helper
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Helper\Product;

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
     * Retrieve Catalog Session instance
     *
     * @return \Magento\Catalog\Model\Session
     */
    protected function _getSession()
    {
        return \Mage::getSingleton('Magento\Catalog\Model\Session');
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
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
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
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
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
     * Retrive add to wishlist url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToWishlistUrl($product)
    {
        $beforeCompareUrl = \Mage::getSingleton('Magento\Catalog\Model\Session')->getBeforeCompareUrl();

        $params = array(
            'product'=>$product->getId(),
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
        );

        return $this->_getUrl('wishlist/index/add', $params);
    }

    /**
     * Retrive add to cart url
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getAddToCartUrl($product)
    {
        $beforeCompareUrl = \Mage::getSingleton('Magento\Catalog\Model\Session')->getBeforeCompareUrl();
        $params = array(
            'product'=>$product->getId(),
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl($beforeCompareUrl)
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
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
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
            \Magento\Core\Controller\Front\Action::PARAM_NAME_URL_ENCODED => $this->getEncodedUrl()
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
            $this->_itemCollection = \Mage::getResourceModel(
                    'Magento\Catalog\Model\Resource\Product\Compare\Item\Collection'
                )
                ->useProductItem(true)
                ->setStoreId(\Mage::app()->getStore()->getId());

            if (\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
                $this->_itemCollection->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());
            } elseif ($this->_customerId) {
                $this->_itemCollection->setCustomerId($this->_customerId);
            } else {
                $this->_itemCollection->setVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
            }

            $this->_itemCollection->setVisibility(
                \Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds()
            );

            /* Price data is added to consider item stock status using price index */
            $this->_itemCollection->addPriceData();

            $this->_itemCollection->addAttributeToSelect('name')
                ->addUrlRewrite()
                ->load();

            /* update compare items count */
            $this->_getSession()->setCatalogCompareItemsCount(count($this->_itemCollection));
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
        if (!$this->_getSession()->hasCatalogCompareItemsCount() && !$this->_customerId) {
            $count = 0;
        } else {
            /** @var $collection \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection */
            $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Compare\Item\Collection')
                ->useProductItem(true);
            if (!$logout && \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()) {
                $collection->setCustomerId(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId());
            } elseif ($this->_customerId) {
                $collection->setCustomerId($this->_customerId);
            } else {
                $collection->setVisitorId(\Mage::getSingleton('Magento\Log\Model\Visitor')->getId());
            }

            /* Price data is added to consider item stock status using price index */
            $collection->addPriceData()
                ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds());

            $count = $collection->getSize();
        }

        $this->_getSession()->setCatalogCompareItemsCount($count);

        return $this;
    }

    /**
     * Retrieve count of items in compare list
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_getSession()->hasCatalogCompareItemsCount()) {
            $this->calculate();
        }

        return $this->_getSession()->getCatalogCompareItemsCount();
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
