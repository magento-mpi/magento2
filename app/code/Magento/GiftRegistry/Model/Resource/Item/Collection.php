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
 * GiftRegistry entity item collection
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftRegistry_Model_Resource_Item_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * @var Magento_GiftRegistry_Model_Item_OptionFactory
     */
    protected $optionFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $productFactory;

    /**
     * List of product IDs
     * Contains IDs of products related to items and their options
     *
     * @var array
     */
    protected $_productIds = array();

    /**
     * @var Magento_Sales_Model_Quote_Config
     */
    protected $salesQuoteConfig;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Sales_Model_Quote_Config $salesQuoteConfig
     * @param Magento_GiftRegistry_Model_Item_OptionFactory $optionFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Sales_Model_Quote_Config $salesQuoteConfig,
        Magento_GiftRegistry_Model_Item_OptionFactory $optionFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
        $this->salesQuoteConfig = $salesQuoteConfig;
        $this->optionFactory = $optionFactory;
        $this->productFactory = $productFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento_GiftRegistry_Model_Item', 'Magento_GiftRegistry_Model_Resource_Item');
    }

    /**
     * Add gift registry filter to collection
     *
     * @param int $entityId
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    public function addRegistryFilter($entityId)
    {
        $this->getSelect()
            ->join(array('e' => $this->getTable('magento_giftregistry_entity')),
                'e.entity_id = main_table.entity_id', 'website_id')
            ->where('main_table.entity_id = ?', (int)$entityId);

        return $this;
    }

    /**
     * Add product filter to collection
     *
     * @param int $productId
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    public function addProductFilter($productId)
    {
        if ((int)$productId > 0) {
            $this->addFieldToFilter('product_id', (int)$productId);
        }

        return $this;
    }

    /**
     * Add item filter to collection
     *
     * @param int|array $itemId
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    public function addItemFilter($itemId)
    {
        if (is_array($itemId)) {
            $this->addFieldToFilter('item_id', array('in' => $itemId));
        } elseif ((int)$itemId > 0) {
            $this->addFieldToFilter('item_id', (int)$itemId);
        }

        return $this;
    }

    /**
     * After load processing
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        // Assign options and products
        $this->_assignOptions();
        $this->_assignProducts();
        $this->resetItemsDataChanged();

        return $this;
    }

    /**
     * Assign options to items
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    protected function _assignOptions()
    {
        $itemIds = array_keys($this->_items);
        $optionCollection = $this->optionFactory->create()->getCollection()
            ->addItemFilter($itemIds);
        foreach ($this as $item) {
            $item->setOptions($optionCollection->getOptionsByItem($item));
        }
        $productIds = $optionCollection->getProductIds();
        $this->_productIds = array_merge($this->_productIds, $productIds);

        return $this;
    }

    /**
     * Assign products to items and their options
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    protected function _assignProducts()
    {
        $productIds = array();
        foreach ($this as $item) {
            $productIds[] = $item->getProductId();
        }
        $this->_productIds = array_merge($this->_productIds, $productIds);

        $productCollection = $this->productFactory->create()->getCollection()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->addIdFilter($this->_productIds)
            ->addAttributeToSelect($this->salesQuoteConfig->getProductAttributes())
            ->addStoreFilter()
            ->addUrlRewrite()
            ->addOptionsToResult();

        foreach ($this as $item) {
            $product = $productCollection->getItemById($item->getProductId());
            if ($product) {
                $product->setCustomOptions(array());
                foreach ($item->getOptions() as $option) {
                    $option->setProduct($productCollection->getItemById($option->getProductId()));
                }
                $item->setProduct($product);
                $item->setProductName($product->getName());
                $item->setProductSku($product->getSku());
                $item->setProductPrice($product->getPrice());
            } else {
                $item->isDeleted(true);
            }
        }
        return $this;
    }

    /**
     * Update items custom price (Depends on custom options)
     *
     * @return Magento_GiftRegistry_Model_Resource_Item_Collection
     */
    public function updateItemAttributes()
    {
        foreach ($this->getItems() as $item) {
            $product = $item->getProduct();
            $product->setSkipCheckRequiredOption(true);
            $product->getStore()->setWebsiteId($item->getWebsiteId());
            $product->setCustomOptions($item->getOptionsByCode());
            $item->setPrice($product->getFinalPrice());
            $simpleOption = $product->getCustomOption('simple_product');
            if ($simpleOption) {
                $item->setSku($simpleOption->getProduct()->getSku());
            } else {
                $item->setSku($product->getSku());
            }
        }
        return $this;
    }
}
