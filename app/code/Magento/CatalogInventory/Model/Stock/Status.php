<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogInventory Stock Status per website Model
 *
 * @method Magento_CatalogInventory_Model_Resource_Stock_Status _getResource()
 * @method Magento_CatalogInventory_Model_Resource_Stock_Status getResource()
 * @method int getProductId()
 * @method Magento_CatalogInventory_Model_Stock_Status setProductId(int $value)
 * @method int getWebsiteId()
 * @method Magento_CatalogInventory_Model_Stock_Status setWebsiteId(int $value)
 * @method int getStockId()
 * @method Magento_CatalogInventory_Model_Stock_Status setStockId(int $value)
 * @method float getQty()
 * @method Magento_CatalogInventory_Model_Stock_Status setQty(float $value)
 * @method int getStockStatus()
 * @method Magento_CatalogInventory_Model_Stock_Status setStockStatus(int $value)
 */
class Magento_CatalogInventory_Model_Stock_Status extends Magento_Core_Model_Abstract
{
    const STATUS_OUT_OF_STOCK       = 0;
    const STATUS_IN_STOCK           = 1;

    /**
     * Product Type Instances cache
     *
     * @var array
     */
    protected $_productTypes = array();

    /**
     * Websites cache
     *
     * @var array
     */
    protected $_websites;

    /**
     * Catalog inventory data
     *
     * @var Magento_CatalogInventory_Helper_Data
     */
    protected $_catalogInventoryData;

    /**
     * @var Magento_Catalog_Model_Product_Type
     */
    protected $_productType;

    /**
     * Store model manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Stock item factory
     *
     * @var Magento_CatalogInventory_Model_Stock_ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Catalog_Model_Product_Type $productType
     * @param Magento_Catalog_Model_Product_Status $productStatus
     * @param Magento_Catalog_Model_Product_Website $productWebsite
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory
     * @param Magento_CatalogInventory_Helper_Data $catalogInventoryData
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Catalog_Model_Product_Type $productType,
        Magento_Catalog_Model_Product_Status $productStatus,
        Magento_Catalog_Model_Product_Website $productWebsite,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory,
        Magento_CatalogInventory_Helper_Data $catalogInventoryData,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->_catalogInventoryData = $catalogInventoryData;
        $this->_productType = $productType;
        $this->_productStatus = $productStatus;
        $this->_productWebsite = $productWebsite;
        $this->_storeManager = $storeManager;
        $this->_stockItemFactory = $stockItemFactory;
    }

    /**
     * Init resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CatalogInventory_Model_Resource_Stock_Status');
    }

    /**
     * Retrieve Product Type Instances
     * as key - type code, value - instance model
     *
     * @return array
     */
    public function getProductTypeInstances()
    {
        if (empty($this->_productTypes)) {
            $productEmulator     = new Magento_Object();

            foreach (array_keys($this->_productType->getTypes()) as $typeId) {
                $productEmulator->setTypeId($typeId);
                $this->_productTypes[$typeId] = $this->_productType->factory($productEmulator);
            }
        }
        return $this->_productTypes;
    }

    /**
     * Retrieve Product Type Instance By Product Type
     *
     * @param string $productType
     * @return Magento_Catalog_Model_Product_Type_Abstract
     */
    public function getProductTypeInstance($productType)
    {
        $types = $this->getProductTypeInstances();
        if (isset($types[$productType])) {
            return $types[$productType];
        }
        return false;
    }

    /**
     * Retrieve website models
     *
     * @return array
     */
    public function getWebsites($websiteId = null)
    {
        if (is_null($this->_websites)) {
            $this->_websites = $this->getResource()->getWebsiteStores();
        }

        $websites = $this->_websites;
        if (!is_null($websiteId) && isset($this->_websites[$websiteId])) {
            $websites = array($websiteId => $this->_websites[$websiteId]);
        }

        return $websites;
    }

    /**
     * Retrieve Default website store Id
     *
     * @param int $websiteId
     * @return int
     */
    public function getWebsiteDefaultStoreId($websiteId)
    {
        $websites = $this->getWebsites();
        if (isset($websites[$websiteId])) {
            return $websites[$websiteId];
        }
        return 0;
    }

    /**
     * Retrieve Product Status Enabled Constant
     *
     * @return int
     */
    public function getProductStatusEnabled()
    {
        return Magento_Catalog_Model_Product_Status::STATUS_ENABLED;
    }

    /**
     * Change Stock Item status process
     *
     * @param Magento_CatalogInventory_Model_Stock_Item $item
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function changeItemStatus(Magento_CatalogInventory_Model_Stock_Item $item)
    {
        $productId  = $item->getProductId();
        if (!$productType = $item->getProductTypeId()) {
            $productType    = $this->getProductType($productId);
        }

        $status     = (int)$item->getIsInStock();
        $qty        = (int)$item->getQty();

        $this->_processChildren($productId, $productType, $qty, $status, $item->getStockId());
        $this->_processParents($productId, $item->getStockId());

        return $this;
    }

    /**
     * Assign Stock Status to Product
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int $stockId
     * @param int $stockStatus
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function assignProduct(Magento_Catalog_Model_Product $product, $stockId = 1, $stockStatus = null)
    {
        if (is_null($stockStatus)) {
            $websiteId = $product->getStore()->getWebsiteId();
            $status = $this->getProductStatus($product->getId(), $websiteId, $stockId);
            $stockStatus = isset($status[$product->getId()]) ? $status[$product->getId()] : null;
        }

        $product->setIsSalable($stockStatus);

        return $this;
    }

    /**
     * Rebuild stock status for all products
     *
     * @param int $websiteId
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function rebuild($websiteId = null)
    {
        $lastProductId = 0;
        while (true) {
            $productCollection = $this->getResource()->getProductCollection($lastProductId);
            if (!$productCollection) {
                break;
            }

            foreach ($productCollection as $productId => $productType) {
                $lastProductId = $productId;
                $this->updateStatus($productId, $productType, $websiteId);
            }
        }

        return $this;
    }

    /**
     * Update product status from stock item
     *
     * @param int $productId
     * @param string $productType
     * @param int $websiteId
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function updateStatus($productId, $productType = null, $websiteId = null)
    {
        if (is_null($productType)) {
            $productType = $this->getProductType($productId);
        }

        /** @var \Magento_CatalogInventory_Model_Stock_Item $item */
        $item = $this->_stockItemFactory->create()->loadByProduct($productId);

        $status  = self::STATUS_IN_STOCK;
        $qty     = 0;
        if ($item->getId()) {
            $status = $item->getIsInStock();
            $qty    = $item->getQty();
        }

        $this->_processChildren($productId, $productType, $qty, $status, $item->getStockId(), $websiteId);
        $this->_processParents($productId, $item->getStockId(), $websiteId);

        return $this;
    }

    /**
     * Process children stock status
     *
     * @param int $productId
     * @param string $productType
     * @param int $qty
     * @param int $status
     * @param int $stockId
     * @param int $websiteId
     *
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    protected function _processChildren(
        $productId,
        $productType,
        $qty = 0,
        $status = self::STATUS_IN_STOCK,
        $stockId = 1,
        $websiteId = null
    ) {
        if ($status == self::STATUS_OUT_OF_STOCK) {
            $this->saveProductStatus($productId, $status, $qty, $stockId, $websiteId);
            return $this;
        }

        $statuses   = array();
        $websites   = $this->getWebsites($websiteId);

        foreach (array_keys($websites) as $websiteId) {
            /* @var $website Magento_Core_Model_Website */
            $statuses[$websiteId] = $status;
        }

        if (!$typeInstance = $this->getProductTypeInstance($productType)) {
            return $this;
        }

        $requiredChildrenIds = $typeInstance->getChildrenIds($productId, true);
        if ($requiredChildrenIds) {
            $childrenIds = array();
            foreach ($requiredChildrenIds as $groupedChildrenIds) {
                $childrenIds = array_merge($childrenIds, $groupedChildrenIds);
            }
            $childrenWebsites = $this->_productWebsite->getWebsites($childrenIds);
            foreach ($websites as $websiteId => $storeId) {
                $childrenStatus = $this->_productStatus->getProductStatus($childrenIds, $storeId);
                $childrenStock  = $this->getProductStatus($childrenIds, $websiteId, $stockId);

                $websiteStatus = $statuses[$websiteId];
                foreach ($requiredChildrenIds as $groupedChildrenIds) {
                    $optionStatus = false;
                    foreach ($groupedChildrenIds as $childId) {
                        if (isset($childrenStatus[$childId])
                            and isset($childrenWebsites[$childId])
                            and in_array($websiteId, $childrenWebsites[$childId])
                            and $childrenStatus[$childId] == $this->getProductStatusEnabled()
                            and isset($childrenStock[$childId])
                            and $childrenStock[$childId] == self::STATUS_IN_STOCK
                        ) {
                            $optionStatus = true;
                        }
                    }
                    $websiteStatus = $websiteStatus && $optionStatus;
                }
                $statuses[$websiteId] = (int)$websiteStatus;
            }
        }

        foreach ($statuses as $websiteId => $websiteStatus) {
            $this->saveProductStatus($productId, $websiteStatus, $qty, $stockId, $websiteId);
        }

        return $this;
    }

    /**
     * Process Parents by child
     *
     * @param int $productId
     * @param int $stockId
     * @param int $websiteId
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    protected function _processParents($productId, $stockId = 1, $websiteId = null)
    {
        $parentIds = array();
        foreach ($this->getProductTypeInstances() as $typeInstance) {
            /* @var $typeInstance Magento_Catalog_Model_Product_Type_Abstract */
            $parentIds = array_merge($parentIds, $typeInstance->getParentIdsByChild($productId));
        }

        if (!$parentIds) {
            return $this;
        }

        $productTypes = $this->getProductsType($parentIds);
        /** @var \Magento_CatalogInventory_Model_Stock_Item $item */
        $item = $this->_stockItemFactory->create();

        foreach ($parentIds as $parentId) {
            $parentType = isset($productTypes[$parentId]) ? $productTypes[$parentId] : null;
            $item->setData(array('stock_id' => $stockId))
                ->setOrigData()
                ->loadByProduct($parentId);
            $status  = self::STATUS_IN_STOCK;
            $qty     = 0;
            if ($item->getId()) {
                $status = $item->getIsInStock();
                $qty    = $item->getQty();
            }

            $this->_processChildren($parentId, $parentType, $qty, $status, $item->getStockId(), $websiteId);
        }

        return $this;
    }

    /**
     * Save product status per website
     * if website is null, saved for all websites
     *
     * @param int $productId
     * @param int $status
     * @param int $qty
     * @param int $stockId
     * @param int|null $websiteId
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function saveProductStatus($productId, $status, $qty = 0, $stockId = 1, $websiteId = null)
    {
        $this->getResource()->saveProductStatus($this, $productId, $status, $qty, $stockId, $websiteId);
        return $this;
    }

    /**
     * Retrieve Product(s) status
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductStatus($productIds, $websiteId, $stockId = 1)
    {
        return $this->getResource()->getProductStatus($productIds, $websiteId, $stockId);
    }

    /**
     * Retrieve Product(s) Data array
     *
     * @param int|array $productIds
     * @param int $websiteId
     * @param int $stockId
     * @return array
     */
    public function getProductData($productIds, $websiteId, $stockId = 1)
    {
        return $this->getResource()->getProductData($productIds, $websiteId, $stockId);
    }

    /**
     * Retrieve Product Type
     *
     * @param int $productId
     * @return string|false
     */
    public function getProductType($productId)
    {
        $types = $this->getResource()->getProductsType($productId);
        if (isset($types[$productId])) {
            return $types[$productId];
        }
        return false;
    }

    /**
     * Retrieve Products Type as array
     * Return array as key product_id, value type
     *
     * @param array|int $productIds
     * @return array
     */
    public function getProductsType($productIds)
    {
        return $this->getResource()->getProductsType($productIds);
    }

    /**
     * Add information about stock status to product collection
     *
     * @param   Magento_Catalog_Model_Resource_Product_Collection $productCollection
     * @param   int|null $websiteId
     * @param   int|null $stockId
     * @return  Magento_CatalogInventory_Model_Stock_Status
     */
    public function addStockStatusToProducts($productCollection, $websiteId = null, $stockId = null)
    {
        if ($stockId === null) {
            $stockId = Magento_CatalogInventory_Model_Stock::DEFAULT_STOCK_ID;
        }
        if ($websiteId === null) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            if ((int)$websiteId == 0 && $productCollection->getStoreId()) {
                $websiteId = $this->_storeManager->getStore($productCollection->getStoreId())->getWebsiteId();
            }
        }
        $productIds = array();
        foreach ($productCollection as $product) {
            $productIds[] = $product->getId();
        }

        if (!empty($productIds)) {
            $stockStatuses = $this->_getResource()->getProductStatus($productIds, $websiteId, $stockId);
            foreach ($stockStatuses as $productId => $status) {
                if ($product = $productCollection->getItemById($productId)) {
                    $product->setIsSalable($status);
                }
            }
        }

        /* back compatible stock item */
        foreach ($productCollection as $product) {
            $object = new Magento_Object(array('is_in_stock' => $product->getData('is_salable')));
            $product->setStockItem($object);
        }

        return $this;
    }

    /**
     * Add stock status to prepare index select
     *
     * @param Magento_DB_Select $select
     * @param Magento_Core_Model_Website $website
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function addStockStatusToSelect(Magento_DB_Select $select, Magento_Core_Model_Website $website)
    {
        $this->_getResource()->addStockStatusToSelect($select, $website);
        return $this;
    }

    /**
     * Add stock status limitation to catalog product price index select object
     *
     * @param Magento_DB_Select $select
     * @param string|Zend_Db_Expr $entityField
     * @param string|Zend_Db_Expr $websiteField
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function prepareCatalogProductIndexSelect(Magento_DB_Select $select, $entityField, $websiteField)
    {
        if ($this->_catalogInventoryData->isShowOutOfStock()) {
            return $this;
        }

        $this->_getResource()->prepareCatalogProductIndexSelect($select, $entityField, $websiteField);

        return $this;
    }

    /**
     * Add only is in stock products filter to product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Magento_CatalogInventory_Model_Stock_Status
     */
    public function addIsInStockFilterToCollection($collection)
    {
        $this->_getResource()->addIsInStockFilterToCollection($collection);
        return $this;
    }

    /**
     * Get options for stock attribute in product creation
     *
     * @return array
     */
    static public function getAllOptions()
    {
        return array(
            array(
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_IN_STOCK,
                'label' => __('In Stock'),
            ),
            array(
                'value' => Magento_CatalogInventory_Model_Stock::STOCK_OUT_OF_STOCK,
                'label' => __('Out of Stock')
            ),
        );
    }
}
