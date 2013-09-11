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
namespace Magento\Catalog\Model;

class Observer
{
    /**
     * Process catalog ata related with store data changes
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Catalog\Model\Observer
     */
    public function storeEdit(\Magento\Event\Observer $observer)
    {
        /** @var $store \Magento\Core\Model\Store */
        $store = $observer->getEvent()->getStore();
        if ($store->dataHasChangedFor('group_id')) {
            \Mage::app()->reinitStores();
            /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
            $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
            if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')
                    ->synchronize(null, array($store->getId()));
            }
            \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Product')->refreshEnabledIndex($store);
        }
        return $this;
    }

    /**
     * Process catalog data related with new store
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Catalog\Model\Observer
     */
    public function storeAdd(\Magento\Event\Observer $observer)
    {
        /* @var $store \Magento\Core\Model\Store */
        $store = $observer->getEvent()->getStore();
        \Mage::app()->reinitStores();
        \Mage::getConfig()->reinit();
        /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
        $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')
                ->synchronize(null, array($store->getId()));
        }
        \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product')->refreshEnabledIndex($store);
        return $this;
    }

    /**
     * Process catalog data related with store group root category
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Catalog\Model\Observer
     */
    public function storeGroupSave(\Magento\Event\Observer $observer)
    {
        /* @var $group \Magento\Core\Model\Store\Group */
        $group = $observer->getEvent()->getGroup();
        if ($group->dataHasChangedFor('root_category_id') || $group->dataHasChangedFor('website_id')) {
            \Mage::app()->reinitStores();
            foreach ($group->getStores() as $store) {
                /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
                $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
                if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
                    \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')
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
     * @return \Magento\Catalog\Model\Observer
     */
    public function storeDelete(\Magento\Event\Observer $observer)
    {
        /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
        $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $store = $observer->getEvent()->getStore();
            \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')->deleteStores($store->getId());
        }
        return $this;
    }

    /**
     * Process catalog data after category move
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Catalog\Model\Observer
     */
    public function categoryMove(\Magento\Event\Observer $observer)
    {
        $categoryId = $observer->getEvent()->getCategoryId();
        $prevParentId = $observer->getEvent()->getPrevParentId();
        $parentId = $observer->getEvent()->getParentId();
        /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
        $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')
                ->move($categoryId, $prevParentId, $parentId);
        }
        return $this;
    }

    /**
     * Process catalog data after products import
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Catalog\Model\Observer
     */
    public function catalogProductImportAfter(\Magento\Event\Observer $observer)
    {
        \Mage::getModel('Magento\Catalog\Model\Url')->refreshRewrites();
        \Mage::getResourceSingleton('Magento\Catalog\Model\Resource\Category')->refreshProductIndex();
        return $this;
    }

    /**
     * Catalog Product Compare Items Clean
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Catalog\Model\Observer
     */
    public function catalogProductCompareClean(\Magento\Event\Observer $observer)
    {
        \Mage::getModel('Magento\Catalog\Model\Product\Compare\Item')->clean();
        return $this;
    }

    /**
     * After save event of category
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Catalog\Model\Observer
     */
    public function categorySaveAfter(\Magento\Event\Observer $observer)
    {
        /** @var $categoryFlatHelper \Magento\Catalog\Helper\Category\Flat */
        $categoryFlatHelper = \Mage::helper('Magento\Catalog\Helper\Category\Flat');
        if ($categoryFlatHelper->isAvailable() && $categoryFlatHelper->isBuilt()) {
            $category = $observer->getEvent()->getCategory();
            \Mage::getResourceModel('Magento\Catalog\Model\Resource\Category\Flat')->synchronize($category);
        }
        return $this;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Catalog\Model\Observer
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(\Magento\Event\Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result  = $observer->getEvent()->getData('result');
        $result->isAllowed = \Mage::helper('Magento\Catalog\Helper\Data')->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }

    /**
     * Cron job method for product prices to reindex
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function reindexProductPrices(\Magento\Cron\Model\Schedule $schedule)
    {
        $indexProcess = \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalog_product_price');
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
            \Mage::helper('Magento\Catalog\Helper\Category')->getStoreCategories(),
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
                'url' => \Mage::helper('Magento\Catalog\Helper\Category')->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new \Magento\Data\Tree\Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if (\Mage::helper('Magento\Catalog\Helper\Category\Flat')->isEnabled()) {
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
        $catalogLayer = \Mage::getSingleton('Magento\Catalog\Model\Layer');
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
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            \Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE,
        );
        $product = $observer->getProduct();
        $attributes = $observer->getRequest()->getParam('attributes');
        if (!empty($attributes)) {
            $product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_CONFIGURABLE);
        } elseif (in_array($product->getTypeId(), $switchableTypes)) {
            $product->setTypeInstance(null);
            $product->setTypeId($product->hasIsVirtual()
                ? \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL
                : \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            );
        }
    }
}
