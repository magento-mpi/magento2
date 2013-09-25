<?php
/**
 * SKU failed grid collection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_AdvancedCheckout_Model_Resource_Sku_Errors_Grid_Collection extends Magento_Data_Collection
{
    /**
     * @var Magento_AdvancedCheckout_Model_Cart
     */
    protected $_cart;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_productModel;

    /**
     * @var Magento_CatalogInventory_Model_Stock_Status
     */
    protected $_inventoryModel;

    /**
     * @var Magento_Core_Helper_Data
     */
    protected $_coreHelper;

    /**
     * @param Magento_AdvancedCheckout_Model_Cart $cart
     * @param Magento_Catalog_Model_Product $productModel
     * @param Magento_CatalogInventory_Model_Stock_Status $catalogInventory
     * @param Magento_Core_Helper_Data $coreHelper
     * @param Magento_Core_Model_EntityFactory $entityFactory
     */
    public function __construct(
        Magento_AdvancedCheckout_Model_Cart $cart,
        Magento_Catalog_Model_Product $productModel,
        Magento_CatalogInventory_Model_Stock_Status $catalogInventory,
        Magento_Core_Helper_Data $coreHelper,
        Magento_Core_Model_EntityFactory $entityFactory
    ) {
        $this->_cart = $cart;
        $this->_productModel = $productModel;
        $this->_inventoryModel = $catalogInventory;
        $this->_coreHelper = $coreHelper;
        parent::__construct($entityFactory);
    }

    /**
     * @param bool $printQuery
     * @param bool $logQuery
     * @return Magento_AdvancedCheckout_Model_Resource_Sku_Errors_Grid_Collection
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
                $item = new Magento_Object();
                $item->setCode($affectedItem['code']);
                if (isset($affectedItem['error'])) {
                    $item->setError($affectedItem['error']);
                }
                $item->addData($affectedItem['item']);
                $item->setId($item->getSku());
                /* @var $product Magento_Catalog_Model_Product */
                $product = $this->_productModel;
                if (isset($affectedItem['item']['id'])) {
                    $productId = $affectedItem['item']['id'];
                    $item->setProductId($productId);
                    $product->load($productId);
                    /* @var $stockStatus Magento_CatalogInventory_Model_Stock_Status */
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

