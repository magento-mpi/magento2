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
 * Catalog Observer
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Observer
{
    /**
     * Catalog category flat
     *
     * @var Magento_Catalog_Helper_Category_Flat
     */
    protected $_catalogCategoryFlat = null;

    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * Catalog category
     *
     * @var Magento_Catalog_Helper_Category
     */
    protected $_catalogCategory = null;
    
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Catalog_Helper_Category $catalogCategory
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Catalog_Helper_Category $catalogCategory,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
        $this->_catalogCategory = $catalogCategory;
        $this->_catalogData = $catalogData;
        $this->_catalogCategoryFlat = $catalogCategoryFlat;
    }

    /**
     * Process catalog ata related with store data changes
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeEdit(Magento_Event_Observer $observer)
    {
        /** @var $store Magento_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        if ($store->dataHasChangedFor('group_id')) {
            Mage::app()->reinitStores();
            /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
            $categoryFlatHelper = $this->_catalogCategoryFlat;
            if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')
                    ->synchronize(null, array($store->getId()));
            }
            Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product')->refreshEnabledIndex($store);
        }
        return $this;
    }

    /**
     * Process catalog data related with new store
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeAdd(Magento_Event_Observer $observer)
    {
        /* @var $store Magento_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        Mage::app()->reinitStores();
        $this->_coreConfig->reinit();
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = $this->_catalogCategoryFlat;
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')
                ->synchronize(null, array($store->getId()));
        }
        Mage::getResourceModel('Magento_Catalog_Model_Resource_Product')->refreshEnabledIndex($store);
        return $this;
    }

    /**
     * Process catalog data related with store group root category
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeGroupSave(Magento_Event_Observer $observer)
    {
        /* @var $group Magento_Core_Model_Store_Group */
        $group = $observer->getEvent()->getGroup();
        if ($group->dataHasChangedFor('root_category_id') || $group->dataHasChangedFor('website_id')) {
            Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
                $categoryFlatHelper = $this->_catalogCategoryFlat;
                if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                    Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')
                        ->synchronize(null, array($store->getId()));
                }
            }
        }
        return $this;
    }

    /**
     * Process delete of store
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function storeDelete(Magento_Event_Observer $observer)
    {
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = $this->_catalogCategoryFlat;
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $store = $observer->getEvent()->getStore();
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')->deleteStores($store->getId());
        }
        return $this;
    }

    /**
     * Process catalog data after category move
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function categoryMove(Magento_Event_Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = $this->_catalogCategoryFlat;
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')
                ->move($categoryId, $prevParentId, $parentId);
        }
        return $this;
    }

    /**
     * Process catalog data after products import
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function catalogProductImportAfter(Magento_Event_Observer $observer)
    {
        Mage::getModel('Magento_Catalog_Model_Url')->refreshRewrites();
        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category')->refreshProductIndex();
        return $this;
    }

    /**
     * After save event of category
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function categorySaveAfter(Magento_Event_Observer $observer)
    {
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = $this->_catalogCategoryFlat;
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')->synchronize($category);
        }
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(Magento_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result  = $observer->getEvent()->getData('result');
        $result->isAllowed = $this->_catalogData->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }

    /**
     * Cron job method for product prices to reindex
     *
     * @param Magento_Cron_Model_Schedule $schedule
     */
    public function reindexProductPrices(Magento_Cron_Model_Schedule $schedule)
    {
        $indexProcess = Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalog_product_price');
        if ($indexProcess) {
            $indexProcess->reindexAll();
        }
    }

    /**
     * Adds catalog categories to top menu
     *
     * @param Magento_Event_Observer $observer
     */
    public function addCatalogToTopmenuItems(Magento_Event_Observer $observer)
    {
        $this->_addCategoriesToMenu(
            $this->_catalogCategory->getStoreCategories(),
            $observer->getMenu()
        );
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param Magento_Data_Tree_Node_Collection|array $categories
     * @param Magento_Data_Tree_Node $parentCategoryNode
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode)
    {
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $tree = $parentCategoryNode->getTree();
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => $this->_catalogCategory->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new Magento_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if ($this->_catalogCategoryFlat->isEnabled()) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode);
        }
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param Magento_Data_Tree_Node $category
     * @return bool
     */
    protected function _isActiveMenuCategory($category)
    {
        $catalogLayer = Mage::getSingleton('Magento_Catalog_Model_Layer');
        if (!$catalogLayer) {
            return false;
        }

        $currentCategory = $catalogLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }

    /**
     * Change product type on the fly depending on selected options
     *
     * @param Magento_Event_Observer $observer
     */
    public function transitionProductType(Magento_Event_Observer $observer)
    {
        $switchableTypes = array(
            Magento_Catalog_Model_Product_Type::TYPE_SIMPLE,
            Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL,
            Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE,
        );
        $product = $observer->getProduct();
        $attributes = $observer->getRequest()->getParam('attributes');
        if (!empty($attributes)) {
            $product->setTypeId(Magento_Catalog_Model_Product_Type::TYPE_CONFIGURABLE);
        } elseif (in_array($product->getTypeId(), $switchableTypes)) {
            $product->setTypeInstance(null);
            $product->setTypeId($product->hasIsVirtual()
                ? Magento_Catalog_Model_Product_Type::TYPE_VIRTUAL
                : Magento_Catalog_Model_Product_Type::TYPE_SIMPLE
            );
        }
    }
}
