<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog layer model integrated with search engine
 */
class Magento_Search_Model_Search_Layer extends Magento_CatalogSearch_Model_Layer
{
    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineProvider
     */
    protected $_engineProvider;

    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData;

    /**
     * @var Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param Magento_Search_Helper_Data $searchData
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Product_Attribute_CollectionFactory $collectionFactory,
        Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider,
        Magento_Search_Helper_Data $searchData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        Magento_Search_Helper_Data $searchData,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_engineProvider = $engineProvider;
        $this->_searchData = $searchData;
        $this->_storeManager = $storeManager;
        $this->_searchData = $searchData;
        parent::__construct($coreRegistry, $fulltextCollectionFactory, $catalogProductVisibility, $catalogConfig,
            $storeManager, $catalogSearchData, $data);
    }

    /**
     * Retrieve current layer product collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_engineProvider->get()->getResultCollection();
            $collection->setStoreId($this->getCurrentCategory()->getStoreId());
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }

        return $collection;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = array_merge($additionalTags, array(
            Magento_Catalog_Model_Category::CACHE_TAG . $this->getCurrentCategory()->getId() . '_SEARCH'
        ));

        return parent::getStateTags($additionalTags);
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return Magento_Catalog_Model_Resource_Product_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        $setIds = $this->_getSetIds();
        if (!$setIds) {
            return array();
        }
        /* @var $collection Magento_Catalog_Model_Resource_Product_Attribute_Collection */
        $collection = $this->_collectionFactory->create()
            ->setItemObjectClass('Magento_Catalog_Model_Resource_Eav_Attribute');

        if ($this->_searchData->getTaxInfluence()) {
            $collection->removePriceFilter();
        }

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel($this->_storeManager->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
}
