<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogSearch_Model_Layer extends Magento_Catalog_Model_Layer
{
    const XML_PATH_DISPLAY_LAYER_COUNT = 'catalog/search/use_layered_navigation_count';

    /**
     * Catalog search data
     *
     * @var Magento_CatalogSearch_Helper_Data
     */
    protected $_catalogSearchData = null;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog config
     *
     * @var Magento_Catalog_Model_Config
     */
    protected $_catalogConfig;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Fulltext collection factory
     *
     * @var Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory
     */
    protected $_fulltextCollectionFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_CatalogSearch_Helper_Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_CatalogSearch_Model_Resource_Fulltext_CollectionFactory $fulltextCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_CatalogSearch_Helper_Data $catalogSearchData,
        array $data = array()
    ) {
        $this->_fulltextCollectionFactory = $fulltextCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogConfig = $catalogConfig;
        $this->_storeManager = $storeManager;
        $this->_catalogSearchData = $catalogSearchData;
        parent::__construct($coreRegistry, $data);
    }

    /**
     * Get current layer product collection
     *
     * @return Magento_Catalog_Model_Resource_Eav_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (isset($this->_productCollections[$this->getCurrentCategory()->getId()])) {
            $collection = $this->_productCollections[$this->getCurrentCategory()->getId()];
        } else {
            $collection = $this->_fulltextCollectionFactory->create();
            $this->prepareProductCollection($collection);
            $this->_productCollections[$this->getCurrentCategory()->getId()] = $collection;
        }
        return $collection;
    }

    /**
     * Prepare product collection
     *
     * @param Magento_Catalog_Model_Resource_Eav_Resource_Product_Collection $collection
     * @return Magento_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addSearchFilter($this->_catalogSearchData->getQuery()->getQueryText())
            ->setStore($this->_storeManager->getStore())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addUrlRewrite()
            ->setVisibility($this->_catalogProductVisibility->getVisibleInSearchIds());

        return $this;
    }

    /**
     * Get layer state key
     *
     * @return string
     */
    public function getStateKey()
    {
        if ($this->_stateKey === null) {
            $this->_stateKey = 'Q_' . $this->_catalogSearchData->getQuery()->getId()
                . '_'. parent::getStateKey();
        }
        return $this->_stateKey;
    }

    /**
     * Get default tags for current layer state
     *
     * @param   array $additionalTags
     * @return  array
     */
    public function getStateTags(array $additionalTags = array())
    {
        $additionalTags = parent::getStateTags($additionalTags);
        $additionalTags[] = Magento_CatalogSearch_Model_Query::CACHE_TAG;
        return $additionalTags;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   Magento_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection $collection
     * @return  Magento_Catalog_Model_Resource_Eav_Resource_Product_Attribute_Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableInSearchFilter()
            ->addVisibleFilter();
        return $collection;
    }

    /**
     * Prepare attribute for use in layered navigation
     *
     * @param   Magento_Eav_Model_Entity_Attribute $attribute
     * @return  Magento_Eav_Model_Entity_Attribute
     */
    protected function _prepareAttribute($attribute)
    {
        $attribute = parent::_prepareAttribute($attribute);
        $attribute->setIsFilterable(Magento_Catalog_Model_Layer_Filter_Attribute::OPTIONS_ONLY_WITH_RESULTS);
        return $attribute;
    }
}
