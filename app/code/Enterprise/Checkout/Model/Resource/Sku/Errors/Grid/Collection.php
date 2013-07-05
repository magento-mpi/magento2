<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sku errors grid collection resource
 *
 * @category   Enterprise
 * @package    Enterprise_Checkout
 */
class Enterprise_Checkout_Model_Resource_Sku_Errors_Grid_Collection extends Varien_Data_Collection
{
    /**
     * @var Enterprise_Checkout_Model_Cart
     */
    protected $_cart;

    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_productModel;

    /**
     * @var Mage_CatalogInventory_Model_Stock_Status
     */
    protected $_inventoryModel;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * @param Enterprise_Checkout_Model_Cart $cart
     * @param Mage_Catalog_Model_Product $productModel
     * @param Mage_CatalogInventory_Model_Stock_Status $catalogInventory
     * @param Mage_Core_Helper_Data $coreHelper
     */
    public function __construct(
        Enterprise_Checkout_Model_Cart $cart,
        Mage_Catalog_Model_Product $productModel,
        Mage_CatalogInventory_Model_Stock_Status $catalogInventory,
        Mage_Core_Helper_Data $coreHelper
    ) {
        $this->_cart = $cart;
        $this->_productModel = $productModel;
        $this->_inventoryModel = $catalogInventory;
        $this->_coreHelper = $coreHelper;
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Enterprise_Checkout_Model_Resource_Sku_Errors_Grid_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $parentBlock = $this->_cart;
            foreach ($parentBlock->getFailedItems() as $affectedItem) {
                // Escape user-submitted input
                if (isset($affectedItem['item']['qty'])) {
                    $affectedItem['item']['qty'] = empty($affectedItem['item']['qty'])
                        ? ''
                        : (float)$affectedItem['item']['qty'];
                }
                $item = new Varien_Object();
                $item->setCode($affectedItem['code']);
                if (isset($affectedItem['error'])) {
                    $item->setError($affectedItem['error']);
                }
                $item->addData($affectedItem['item']);
                $item->setId($item->getSku());
                /* @var $product Mage_Catalog_Model_Product */
                $product = $this->_productModel;
                if (isset($affectedItem['item']['id'])) {
                    $productId = $affectedItem['item']['id'];
                    $item->setProductId($productId);
                    $product->load($productId);
                    /* @var $stockStatus Mage_CatalogInventory_Model_Stock_Status */
                    $stockStatus = $this->_inventoryModel;
                    $status = $stockStatus->getProductStatus($productId, $this->getWebsiteId());
                    if (!empty($status[$productId])) {
                        $product->setIsSalable($status[$productId]);
                    }
                    $item->setPrice($this->_coreHelper)->formatPrice($product->getPrice());
                }
                $item->setProduct($product);
                $this->addItem($item);
            }
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * Get current website ID
     *
     * @return int|null|string
     */
    public function getWebsiteId()
    {
        return $this->_cart->getStore()->getWebsiteId();
    }
}

