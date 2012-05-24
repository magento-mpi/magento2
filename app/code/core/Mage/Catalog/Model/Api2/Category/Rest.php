<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Abstract API2 model for category resources
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Model_Api2_Category_Rest extends Mage_Catalog_Model_Api2_Category
{

    /**
     * Current loaded category
     *
     * @var Mage_Catalog_Model_Category
     */
    protected $_category;

    /**
     * Category create only available for admin
     *
     * @param array $data
     */
    protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Category update only available for admin
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Category delete only available for admin
     */
    protected function _delete()
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }

    /**
     * Retrieve category data
     *
     * @return array $data
     */
    protected function _retrieve()
    {
        return $this->_getCategory()->getData();
    }

    /**
     * Retrieve category collection as a tree
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $treeRootCategory = $this->_initTreeRootCategory();
        $this->_fillTreeWithCategories($treeRootCategory);
        return array($treeRootCategory->getData());
    }

    /**
     * Create tree root category based on request params
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _initTreeRootCategory()
    {
        $treeRootId = $this->getRequest()->getParam('root');
        if (is_null($treeRootId)) {
            $store = $this->_getStore();
            $treeRootId = ($store && !$store->isAdmin()) ? $store->getRootCategoryId()
                : Mage_Catalog_Model_Category::TREE_ROOT_ID;
        }
        $treeRootCategory = $this->_initCategory($treeRootId);
        if (!$treeRootCategory) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        if (!$this->_getStore()->isAdmin()) {
            $parentPathIds = explode('/', $treeRootCategory->getPath());
            $doesCategoryBelongToSpecifiedStore = in_array($this->_getStore()->getRootCategoryId(), $parentPathIds);
            if ($treeRootCategory->getParentId() && !$doesCategoryBelongToSpecifiedStore) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
        }
        return $treeRootCategory;
    }

    /**
     * Fill categories tree with category nodes within specified depth
     *
     * @param $treeRootCategory
     */
    protected function _fillTreeWithCategories($treeRootCategory)
    {
        $treeDepth = $this->getRequest()->getParam('depth', 0);
        /** @var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree */
        $tree = Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Category_Tree')->load($treeRootCategory->getId(), $treeDepth);
        /** @var $categoriesCollection Mage_Catalog_Model_Resource_Category_Collection */
        $categoriesCollection = Mage::getResourceModel('Mage_Catalog_Model_Resource_Category_Collection')
            ->addAttributeToSelect('*')->setStoreId($this->_getStore()->getId());

        $treeCategories = $this->_getTreeCategories($tree, $categoriesCollection);
        $treeRootCategory->setData('subcategories', $this->getFilter()->collectionOut($treeCategories));
    }

    /**
     * Get categories as tree
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree $tree
     * @param Mage_Catalog_Model_Resource_Category_Collection $categoriesCollection
     * @return array
     */
    protected function _getTreeCategories($tree, $categoriesCollection)
    {
        $treeCategories = array();
        $tree->addCollectionData($categoriesCollection, true, array(), true, true);
        /** @var $categoryNode Varien_Data_Tree_Node */
        foreach ($tree->getNodes() as $categoryNode) {
            if (!$categoryNode->getParent() && $categoryNode->getIsActive()) {
                $treeCategories[$categoryNode->getId()] = $this->_treeNodeToArray($categoryNode);
            }
        }

        return $treeCategories;
    }

    /**
     * Convert category node to array. Initialize 'subcategories' key with array of subcategories
     *
     * @param Varien_Data_Tree_Node $categoryNode
     * @return array
     */
    protected function _treeNodeToArray(Varien_Data_Tree_Node $categoryNode)
    {
        $result = $categoryNode->getData();
        $result['subcategories'] = array();
        /** @var $childCategoryNode Varien_Data_Tree_Node */
        foreach ($categoryNode->getChildren() as $childCategoryNode) {
            $result['subcategories'][$childCategoryNode->getId()] = $this->_treeNodeToArray($childCategoryNode);
        }
        $result['subcategories'] = $this->getFilter()->collectionOut($result['subcategories']);
        return $result;
    }

    /**
     * Load category by its ID provided in request
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        if (is_null($this->_category)) {
            $categoryId = $this->getRequest()->getParam('id');
            if (!$category = $this->_initCategory($categoryId)) {
                $this->_critical(self::RESOURCE_NOT_FOUND);
            }
            // check if category belongs to specified store
            if ($this->_getStore()->getRootCategoryId()) {
                $parentPathIds = explode('/', $category->getPath());
                $categoryBelongsToStore = in_array($this->_getStore()->getRootCategoryId(), $parentPathIds);
                if (!$categoryBelongsToStore) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            // Check display settings for customers and guests
            if ($this->getApiUser()->getType() != Mage_Api2_Model_Auth_User_Admin::USER_TYPE) {
                if (!$category->getIsActive() || !$category->getParentId()) {
                    $this->_critical(self::RESOURCE_NOT_FOUND);
                }
            }
            $this->_category = $category;
        }
        return $this->_category;
    }

    /**
     * Initilize and return category model. Return false on failure
     *
     * @param int $categoryId
     * @return Mage_Catalog_Model_Category|bool
     */
    protected function _initCategory($categoryId)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = Mage::getModel('Mage_Catalog_Model_Category')->setStoreId($this->_getStore()->getId())->load($categoryId);
        if (!$category->getId()) {
            $category = false;
        } else if ($this->getApiUser()->getType() == Mage_Api2_Model_Auth_User_Admin::USER_TYPE) {
            // Load additional fields for admin
            $this->_initAdditionalCategoryFields($category);
        }
        return $category;
    }

    /**
     * Load additional category fields
     *
     * @param Mage_Catalog_Model_Category $category
     */
    protected function _initAdditionalCategoryFields($category)
    {
        $category->getStoreIds();
        // If available sort by is empty it means that 'Use All Available Attributes' checkbox is set
        $availableSortBy = $category->getAvailableSortBy()
            ? $category->getAvailableSortBy() : array_keys($category->getAvailableSortByOptions());
        $category->setData('available_sort_by', $availableSortBy);
        if (!$category->getData('default_sort_by')) {
            $category->setData('default_sort_by', $category->getDefaultSortBy());
        }
        if (!$category->getData('filter_price_range')
            && $this->_getStore()->getConfig(Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_RANGE_CALCULATION)
                == Mage_Catalog_Model_Layer_Filter_Price::RANGE_CALCULATION_MANUAL) {
            $category->setData('filter_price_range', $this->_getStore()
                ->getConfig(Mage_Catalog_Model_Layer_Filter_Price::XML_PATH_RANGE_STEP));
        }
    }
}
