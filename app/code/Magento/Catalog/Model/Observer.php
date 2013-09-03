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
     * Process catalog ata related with store data changes
     *
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeEdit(\Magento\Event\Observer $observer)
    {
        /** @var $store Magento_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        if ($store->dataHasChangedFor('group_id')) {
            Mage::app()->reinitStores();
            /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
            $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
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
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeAdd(\Magento\Event\Observer $observer)
    {
        /* @var $store Magento_Core_Model_Store */
        $store = $observer->getEvent()->getStore();
        Mage::app()->reinitStores();
        Mage::getConfig()->reinit();
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
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
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function storeGroupSave(\Magento\Event\Observer $observer)
    {
        /* @var $group Magento_Core_Model_Store_Group */
        $group = $observer->getEvent()->getGroup();
        if ($group->dataHasChangedFor('root_category_id') || $group->dataHasChangedFor('website_id')) {
            Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
                $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
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
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function storeDelete(\Magento\Event\Observer $observer)
    {
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $store = $observer->getEvent()->getStore();
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')->deleteStores($store->getId());
        }
        return $this;
    }

    /**
     * Process catalog data after category move
     *
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function categoryMove(\Magento\Event\Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')
                ->move($categoryId, $prevParentId, $parentId);
        }
        return $this;
    }

    /**
     * Process catalog data after products import
     *
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_Catalog_Model_Observer
     */
    public function catalogProductImportAfter(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Catalog_Model_Url')->refreshRewrites();
        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category')->refreshProductIndex();
        return $this;
    }

    /**
     * Catalog Product Compare Items Clean
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function catalogProductCompareClean(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Catalog_Model_Product_Compare_Item')->clean();
        return $this;
    }

    /**
     * After save event of category
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function categorySaveAfter(\Magento\Event\Observer $observer)
    {
        /** @var $categoryFlatHelper Magento_Catalog_Helper_Category_Flat */
        $categoryFlatHelper = Mage::helper('Magento_Catalog_Helper_Category_Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $category = $observer->getEvent()->getCategory();
            Mage::getResourceModel('Magento_Catalog_Model_Resource_Category_Flat')->synchronize($category);
        }
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Catalog_Model_Observer
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(\Magento\Event\Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result  = $observer->getEvent()->getData('result');
        $result->isAllowed = Mage::helper('Magento_Catalog_Helper_Data')->setStoreId($storeId)->isUsingStaticUrlsAllowed();
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
     * @param \Magento\Event\Observer $observer
     */
    public function addCatalogToTopmenuItems(\Magento\Event\Observer $observer)
    {
        $this->_addCategoriesToMenu(
            Mage::helper('Magento_Catalog_Helper_Category')->getStoreCategories(),
            $observer->getMenu()
        );
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param \Magento\Data\Tree\Node\Collection|array $categories
     * @param \Magento\Data\Tree\Node $parentCategoryNode
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
                'url' => Mage::helper('Magento_Catalog_Helper_Category')->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new \Magento\Data\Tree\Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if (Mage::helper('Magento_Catalog_Helper_Category_Flat')->isEnabled()) {
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
     * @param \Magento\Data\Tree\Node $category
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
     * @param \Magento\Event\Observer $observer
     */
    public function transitionProductType(\Magento\Event\Observer $observer)
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
