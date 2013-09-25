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
 * Catalog category
 *
 * @method array getAffectedProductIds()
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Model_Category extends Magento_Catalog_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY                = 'catalog_category';
    /**
     * Category display modes
     */
    const DM_PRODUCT            = 'PRODUCTS';
    const DM_PAGE               = 'PAGE';
    const DM_MIXED              = 'PRODUCTS_AND_PAGE';
    const TREE_ROOT_ID          = 1;

    const CACHE_TAG             = 'catalog_category';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix     = 'catalog_category';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject     = 'category';

    /**
     * Model cache tag for clear cache in after save and after delete
     */
    protected $_cacheTag        = self::CACHE_TAG;

    /**
     * URL Model instance
     *
     * @var Magento_Core_Model_Url
     */
    protected $_url;

    /**
     * URL rewrite model
     *
     * @var Magento_Core_Model_Url_Rewrite
     */
    protected $_urlRewrite;

    /**
     * Use flat resource model flag
     *
     * @var bool
     */
    protected $_useFlatResource = false;

    /**
     * Category design attributes
     *
     * @var array
     */
    protected $_designAttributes  = array(
        'custom_design',
        'custom_design_from',
        'custom_design_to',
        'page_layout',
        'custom_layout_update',
        'custom_apply_to_products'
    );

    /**
     * Category tree model
     *
     * @var Magento_Catalog_Model_Resource_Category_Tree
     */
    protected $_treeModel = null;

    /**
     * Catalog category flat
     *
     * @var Magento_Catalog_Helper_Category_Flat
     */
    protected $_catalogCategoryFlat = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Index indexer
     *
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexIndexer;

    /**
     * Catalog config
     *
     * @var Magento_Catalog_Model_Config
     */
    protected $_catalogConfig;

    /**
     * Product collection factory
     *
     * @var Magento_Catalog_Model_Resource_Product_CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Store collection factory
     *
     * @var Magento_Core_Model_Resource_Store_CollectionFactory
     */
    protected $_storeCollectionFactory;

    /**
     * Url rewrite factory
     *
     * @var Magento_Core_Model_Url_RewriteFactory
     */
    protected $_urlRewriteFactory;

    /**
     * Category factory
     *
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Category tree factory
     *
     * @var Magento_Catalog_Model_Resource_Category_TreeFactory
     */
    protected $_categoryTreeFactory;

    /**
     * Construct
     *
     * @param Magento_Catalog_Model_Resource_Category_Tree $categoryTreeResource
     * @param Magento_Catalog_Model_Resource_Category_TreeFactory $categoryTreeFactory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param Magento_Core_Model_Url_RewriteFactory $urlRewriteFactory
     * @param Magento_Core_Model_Resource_Store_CollectionFactory $storeCollectionFactory
     * @param Magento_Core_Model_UrlInterface $url
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Index_Model_Indexer $indexIndexer
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Category_Tree $categoryTreeResource,
        Magento_Catalog_Model_Resource_Category_TreeFactory $categoryTreeFactory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        Magento_Core_Model_Url_RewriteFactory $urlRewriteFactory,
        Magento_Core_Model_Resource_Store_CollectionFactory $storeCollectionFactory,
        Magento_Core_Model_UrlInterface $url,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Index_Model_Indexer $indexIndexer,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_categoryTreeFactory = $categoryTreeFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogConfig = $catalogConfig;
        $this->_indexIndexer = $indexIndexer;
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_catalogCategoryFlat = $catalogCategoryFlat;
        $this->_treeModel = $categoryTreeResource;
        parent::__construct($storeManager, $context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource mode
     *
     * @return void
     */
    protected function _construct()
    {
        // If Flat Data enabled then use it but only on frontend
        if ($this->_catalogCategoryFlat->isAvailable() && !$this->_storeManager->getStore()->isAdmin()) {
            $this->_init('Magento_Catalog_Model_Resource_Category_Flat');
            $this->_useFlatResource = true;
        } else {
            $this->_init('Magento_Catalog_Model_Resource_Category');
        }
    }

    /**
     * Retrieve URL instance
     *
     * @return Magento_Core_Model_Url
     */
    public function getUrlInstance()
    {
        return $this->_url;
    }

    /**
     * Get url rewrite model
     *
     * @return Magento_Core_Model_Url_Rewrite
     */
    public function getUrlRewrite()
    {
        if (!$this->_urlRewrite) {
            $this->_urlRewrite = $this->_urlRewriteFactory->create();
        }
        return $this->_urlRewrite;
    }

    /**
     * Retrieve category tree model
     *
     * @return Magento_Catalog_Model_Resource_Category_Tree
     */
    public function getTreeModel()
    {
        return $this->_categoryTreeFactory->create();
    }

    /**
     * Enter description here...
     *
     * @return Magento_Catalog_Model_Resource_Category_Tree
     */
    public function getTreeModelInstance()
    {
        return $this->_treeModel;
    }

    /**
     * Move category
     *
     * @param   int $parentId new parent category id
     * @param   int $afterCategoryId category id after which we have put current category
     * @return  Magento_Catalog_Model_Category
     */
    public function move($parentId, $afterCategoryId)
    {
        /**
         * Validate new parent category id. (category model is used for backward
         * compatibility in event params)
         */
        $parent = $this->_categoryFactory->create()
            ->setStoreId($this->getStoreId())
            ->load($parentId);

        if (!$parent->getId()) {
            throw new Magento_Core_Exception(
                __('Sorry, but we can\'t move the category because we can\'t find the new parent category you selected.')
            );
        }

        if (!$this->getId()) {
            throw new Magento_Core_Exception(
                __('Sorry, but we can\'t move the category because we can\'t find the new category you selected.')
            );
        } elseif ($parent->getId() == $this->getId()) {
            throw new Magento_Core_Exception(
                __('We can\'t perform this category move operation because the parent category matches the child category.')
            );
        }

        /**
         * Setting affected category ids for third party engine index refresh
        */
        $this->setMovedCategoryId($this->getId());

        $eventParams = array(
            $this->_eventObject => $this,
            'parent'        => $parent,
            'category_id'   => $this->getId(),
            'prev_parent_id'=> $this->getParentId(),
            'parent_id'     => $parentId
        );
        $moveComplete = false;

        $this->_getResource()->beginTransaction();
        try {
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_before', $eventParams);
            $this->getResource()->changeParent($this, $parent, $afterCategoryId);
            $this->_eventManager->dispatch($this->_eventPrefix . '_move_after', $eventParams);
            $this->_getResource()->commit();

            // Set data for indexer
            $this->setAffectedCategoryIds(array($this->getId(), $this->getParentId(), $parentId));

            $moveComplete = true;
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
        if ($moveComplete) {
            $this->_eventManager->dispatch('category_move', $eventParams);
            $this->_indexIndexer->processEntityAction(
                $this, self::ENTITY, Magento_Index_Model_Event::TYPE_SAVE
            );
            $this->_cacheManager->clean(array(self::CACHE_TAG));
        }

        return $this;
    }

    /**
     * Retrieve default attribute set id
     *
     * @return int
     */
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }

    /**
     * Get category products collection
     *
     * @return Magento_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = $this->_productCollectionFactory->create()
            ->setStoreId($this->getStoreId())
            ->addCategoryFilter($this);
        return $collection;
    }

    /**
     * Retrieve all customer attributes
     *
     * @todo Use with Flat Resource
     * @return array
     */
    public function getAttributes($noDesignAttributes = false)
    {
        $result = $this->getResource()
            ->loadAllAttributes($this)
            ->getSortedAttributes();

        if ($noDesignAttributes){
            foreach ($result as $k=>$a){
                if (in_array($k, $this->_designAttributes)) {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve array of product id's for category
     *
     * array($productId => $position)
     *
     * @return array
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return array();
        }

        $array = $this->getData('products_position');
        if (is_null($array)) {
            $array = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $array);
        }
        return $array;
    }

    /**
     * Retrieve array of store ids for category
     *
     * @return array
     */
    public function getStoreIds()
    {
        if ($this->getInitialSetupFlag()) {
            return array();
        }

        $storeIds = $this->getData('store_ids');
        if ($storeIds) {
            return $storeIds;
        }

        if (!$this->getId()) {
            return array();
        }

        $nodes = array();
        foreach ($this->getPathIds() as $id) {
            $nodes[] = $id;
        }

        $storeIds = array();
        $storeCollection = $this->_storeCollectionFactory->create()->loadByCategoryIds($nodes);
        foreach ($storeCollection as $store) {
            $storeIds[$store->getId()] = $store->getId();
        }

        $entityStoreId = $this->getStoreId();
        if (!in_array($entityStoreId, $storeIds)) {
            array_unshift($storeIds, $entityStoreId);
        }
        if (!in_array(0, $storeIds)) {
            array_unshift($storeIds, 0);
        }

        $this->setData('store_ids', $storeIds);
        return $storeIds;
    }

    /**
     * Return store id.
     *
     * If store id is underfined for category return current active store id
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->hasData('store_id')) {
            return $this->_getData('store_id');
        }
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Set store id
     *
     * @param int|string $storeId
     * @return Magento_Catalog_Model_Category
     */
    public function setStoreId($storeId)
    {
        if (!is_numeric($storeId)) {
            $storeId = $this->_storeManager->getStore($storeId)->getId();
        }
        $this->setData('store_id', $storeId);
        $this->getResource()->setStoreId($storeId);
        return $this;
    }

    /**
     * Get category url
     *
     * @return string
     */
    public function getUrl()
    {
        $url = $this->_getData('url');
        if (is_null($url)) {
            Magento_Profiler::start('REWRITE: '.__METHOD__, array('group' => 'REWRITE', 'method' => __METHOD__));

            if ($this->hasData('request_path') && $this->getRequestPath() != '') {
                $this->setData('url', $this->getUrlInstance()->getDirectUrl($this->getRequestPath()));
                Magento_Profiler::stop('REWRITE: '.__METHOD__);
                return $this->getData('url');
            }

            $rewrite = $this->getUrlRewrite();
            if ($this->getStoreId()) {
                $rewrite->setStoreId($this->getStoreId());
            }
            $idPath = 'category/' . $this->getId();
            $rewrite->loadByIdPath($idPath);

            if ($rewrite->getId()) {
                $this->setData('url', $this->getUrlInstance()->getDirectUrl($rewrite->getRequestPath()));
                Magento_Profiler::stop('REWRITE: '.__METHOD__);
                return $this->getData('url');
            }

            Magento_Profiler::stop('REWRITE: '.__METHOD__);

            $this->setData('url', $this->getCategoryIdUrl());
            return $this->getData('url');
        }
        return $url;
    }

    /**
     * Retrieve category id URL
     *
     * @return string
     */
    public function getCategoryIdUrl()
    {
        Magento_Profiler::start('REGULAR: ' . __METHOD__, array('group' => 'REGULAR', 'method' => __METHOD__));
        $urlKey = $this->getUrlKey() ? $this->getUrlKey() : $this->formatUrlKey($this->getName());
        $url = $this->getUrlInstance()->getUrl('catalog/category/view', array(
            's' => $urlKey,
            'id' => $this->getId(),
        ));
        Magento_Profiler::stop('REGULAR: ' . __METHOD__);
        return $url;
    }

    /**
     * Format URL key from name or defined key
     *
     * @param string $str
     * @return string
     */
    public function formatUrlKey($str)
    {
        $str = $this->_coreData->removeAccents($str);
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', $str);
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        return $urlKey;
    }

    /**
     * Retrieve image URL
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = false;
        $image = $this->getImage();
        if ($image) {
            $url = $this->_storeManager->getStore()->getBaseUrl(Magento_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/category/' . $image;
        }
        return $url;
    }

    /**
     * Retrieve URL path
     *
     * @return string
     */
    public function getUrlPath()
    {
        $path = $this->getData('url_path');
        if ($path) {
            return $path;
        }

        $path = $this->getUrlKey();

        if ($this->getParentId()) {
            $parentPath = $this->_categoryFactory->create()
                ->load($this->getParentId())->getCategoryPath();
            $path = $parentPath . '/' . $path;
        }

        $this->setUrlPath($path);

        return $path;
    }

    /**
     * Get parent category object
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getParentCategory()
    {
        if (!$this->hasData('parent_category')) {
            $this->setData('parent_category', $this->_categoryFactory->create()->load($this->getParentId()));
        }
        return $this->_getData('parent_category');
    }

    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        return intval(array_pop($parentIds));
    }

    /**
     * Get all parent categories ids
     *
     * @return array
     */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), array($this->getId()));
    }

    /**
     * Retrieve dates for custom design (from & to)
     *
     * @return array
     */
    public function getCustomDesignDate()
    {
        $result = array();
        $result['from'] = $this->getData('custom_design_from');
        $result['to'] = $this->getData('custom_design_to');

        return $result;
    }

    /**
     * Retrieve design attributes array
     *
     * @return array
     */
    public function getDesignAttributes()
    {
        $result = array();
        foreach ($this->_designAttributes as $attrName) {
            $result[] = $this->_getAttribute($attrName);
        }
        return $result;
    }

    /**
     * Retrieve attribute by code
     *
     * @param string $attributeCode
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     */
    private function _getAttribute($attributeCode)
    {
        if (!$this->_useFlatResource) {
            $attribute = $this->getResource()->getAttribute($attributeCode);
        } else {
            $attribute = $this->_catalogConfig
                ->getAttribute(self::ENTITY, $attributeCode);
        }
        return $attribute;
    }

    /**
     * Get all children categories IDs
     *
     * @param boolean $asArray return result as array instead of comma-separated list of IDs
     * @return array|string
     */
    public function getAllChildren($asArray = false)
    {
        $children = $this->getResource()->getAllChildren($this);
        if ($asArray) {
            return $children;
        } else {
            return implode(',', $children);
        }
    }

    /**
     * Retrieve children ids comma separated
     *
     * @return string
     */
    public function getChildren()
    {
        return implode(',', $this->getResource()->getChildren($this, false));
    }

    /**
     * Retrieve Stores where isset category Path
     * Return comma separated string
     *
     * @return string
     */
    public function getPathInStore()
    {
        $result = array();
        $path = array_reverse($this->getPathIds());
        foreach ($path as $itemId) {
            if ($itemId == $this->_storeManager->getStore()->getRootCategoryId()) {
                break;
            }
            $result[] = $itemId;
        }
        return implode(',', $result);
    }

    /**
     * Check category id existing
     *
     * @param   int $id
     * @return  bool
     */
    public function checkId($id)
    {
        return $this->_getResource()->checkId($id);
    }

    /**
     * Get array categories ids which are part of category path
     * Result array contain id of current category because it is part of the path
     *
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if (is_null($ids)) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }
        return $ids;
    }

    /**
     * Retrieve level
     *
     * @return int
     */
    public function getLevel()
    {
        if (!$this->hasLevel()) {
            return count(explode('/', $this->getPath())) - 1;
        }
        return $this->getData('level');
    }

    /**
     * Verify category ids
     *
     * @param array $ids
     * @return bool
     */
    public function verifyIds(array $ids)
    {
        return $this->getResource()->verifyIds($ids);
    }

    /**
     * Retrieve Is Category has children flag
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->_getResource()->getChildrenAmount($this) > 0;
    }

    /**
     * Retrieve Request Path
     *
     * @return string
     */
    public function getRequestPath()
    {
        return $this->_getData('request_path');
    }

    /**
     * Retrieve Name data wrapper
     *
     * @return string
     */
    public function getName()
    {
        return $this->_getData('name');
    }

    /**
     * Before delete process
     *
     * @return Magento_Catalog_Model_Category
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        if ($this->getResource()->isForbiddenToDelete($this->getId())) {
            throw new Magento_Core_Exception("Can't delete root category.");
        }
        return parent::_beforeDelete();
    }

    /**
     * Retrieve anchors above
     *
     * @return array
     */
    public function getAnchorsAbove()
    {
        $anchors = array();
        $path = $this->getPathIds();

        if (in_array($this->getId(), $path)) {
            unset($path[array_search($this->getId(), $path)]);
        }

        if ($this->_useFlatResource) {
            $anchors = $this->_getResource()->getAnchorsAbove($path, $this->getStoreId());
        } else {
            if (!$this->_coreRegistry->registry('_category_is_anchor_attribute')) {
                $model = $this->_getAttribute('is_anchor');
                $this->_coreRegistry->register('_category_is_anchor_attribute', $model);
            }

            $isAnchorAttribute = $this->_coreRegistry->registry('_category_is_anchor_attribute');
            if ($isAnchorAttribute) {
                $anchors = $this->getResource()->findWhereAttributeIs($path, $isAnchorAttribute, 1);
            }
        }
        return $anchors;
    }

    /**
     * Retrieve count products of category
     *
     * @return int
     */
    public function getProductCount()
    {
        if (!$this->hasProductCount()) {
            $count = $this->_getResource()->getProductCount($this); // load product count
            $this->setData('product_count', $count);
        }
        return $this->getData('product_count');
    }

    /**
     * Retrieve categories by parent
     *
     * @param int $parent
     * @param int $recursionLevel
     * @param bool $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return mixed
     */
    public function getCategories($parent, $recursionLevel = 0, $sorted = false, $asCollection = false, $toLoad = true)
    {
        $categories = $this->getResource()
            ->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);
        return $categories;
    }

    /**
     * Return parent categories of current category
     *
     * @return array
     */
    public function getParentCategories()
    {
        return $this->getResource()->getParentCategories($this);
    }

    /**
     * Retuen children categories of current category
     *
     * @return array
     */
    public function getChildrenCategories()
    {
        return $this->getResource()->getChildrenCategories($this);
    }

    /**
     * Return parent category of current category with own custom design settings
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getParentDesignCategory()
    {
        return $this->getResource()->getParentDesignCategory($this);
    }

    /**
     * Check category is in Root Category list
     *
     * @return bool
     */
    public function isInRootCategoryList()
    {
        // TODO there are bugs in resource models' methods, store_id set to model o/andr to resource model are ignored
        return $this->getResource()->isInRootCategoryList($this);
    }

    /**
     * Retrieve Available int Product Listing sort by
     *
     * @return null|array
     */
    public function getAvailableSortBy()
    {
        $available = $this->getData('available_sort_by');
        if (empty($available)) {
            return array();
        }
        if ($available && !is_array($available)) {
            $available = explode(',', $available);
        }
        return $available;
    }

    /**
     * Retrieve Available Product Listing  Sort By
     * code as key, value - name
     *
     * @return array
     */
    public function getAvailableSortByOptions()
    {
        $availableSortBy = array();
        $defaultSortBy   = $this->_catalogConfig
            ->getAttributeUsedForSortByArray();
        if ($this->getAvailableSortBy()) {
            foreach ($this->getAvailableSortBy() as $sortBy) {
                if (isset($defaultSortBy[$sortBy])) {
                    $availableSortBy[$sortBy] = $defaultSortBy[$sortBy];
                }
            }
        }

        if (!$availableSortBy) {
            $availableSortBy = $defaultSortBy;
        }

        return $availableSortBy;
    }

    /**
     * Retrieve Product Listing Default Sort By
     *
     * @return string
     */
    public function getDefaultSortBy()
    {
        if (!$sortBy = $this->getData('default_sort_by')) {
            $sortBy = $this->_catalogConfig
                ->getProductListDefaultSortBy($this->getStoreId());
        }
        $available = $this->getAvailableSortByOptions();
        if (!isset($available[$sortBy])) {
            $sortBy = array_keys($available);
            $sortBy = $sortBy[0];
        }

        return $sortBy;
    }

    /**
     * Validate attribute values
     *
     * @throws Magento_Eav_Model_Entity_Attribute_Exception
     * @return bool|array
     */
    public function validate()
    {
        return $this->_getResource()->validate($this);
    }

    /**
     * Init indexing process after category save
     *
     * @return Magento_Catalog_Model_Category
     */
    protected function _afterSave()
    {
        $result = parent::_afterSave();
        $this->_indexIndexer->processEntityAction(
            $this, self::ENTITY, Magento_Index_Model_Event::TYPE_SAVE
        );
        return $result;
    }
}
