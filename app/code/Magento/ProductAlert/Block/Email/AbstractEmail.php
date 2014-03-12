<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Email;

/**
 * Product Alert Abstract Email Block
 *
 * @category   Magento
 * @package    Magento_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractEmail extends \Magento\View\Element\Template
{
    /**
     * Product collection array
     *
     * @var array
     */
    protected $_products = array();

    /**
     * Current Store scope object
     *
     * @var \Magento\Core\Model\Store
     */
    protected $_store;

    /**
     * Set Store scope
     *
     * @param int|string|\Magento\Core\Model\Website|\Magento\Core\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        if ($store instanceof \Magento\Core\Model\Website) {
            $store = $store->getDefaultStore();
        }
        if (!$store instanceof \Magento\Core\Model\Store) {
            $store = $this->_storeManager->getStore($store);
        }

        $this->_store = $store;

        return $this;
    }

    /**
     * Retrieve current store object
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = $this->_storeManager->getStore();
        }
        return $this->_store;
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $format             Format price to currency format
     * @param boolean $includeContainer   Enclose into <span class="price"><span>
     * @return float
     */
    public function formatPrice($price, $format = true, $includeContainer = true)
    {
        return $this->getStore()->convertPrice($price, $format, $includeContainer);
    }

    /**
     * Reset product collection
     *
     * @return void
     */
    public function reset()
    {
        $this->_products = array();
    }

    /**
     * Add product to collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function addProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_products[$product->getId()] = $product;
    }

    /**
     * Retrieve product collection array
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Get store url params
     *
     * @return array
     */
    protected function _getUrlParams()
    {
        return array(
            '_scope'        => $this->getStore(),
            '_scope_to_url' => true
        );
    }
}
