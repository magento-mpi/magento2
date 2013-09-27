<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Events edit form select categories
 */
class Magento_CatalogEvent_Block_Adminhtml_Event_Edit_Category extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'categories.phtml';

    /**
     * Category model factory
     *
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Magento_Catalog_Model_Resource_Category_Tree $categoryTree
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Resource_Category_Tree $categoryTree,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Catalog_Model_CategoryFactory $categoryFactory,
        array $data = array()
    ) {
        parent::__construct($categoryTree, $coreData, $context, $registry, $data);

        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Get categories tree as recursive array
     *
     * @param int $parentId
     * @param bool $asJson
     * @param int $recursionLevel
     * @return array
     */
    public function getTreeArray($parentId = null, $asJson = false, $recursionLevel = 3)
    {
        $result = array();
        if ($parentId) {
            /** @var Magento_Catalog_Model_Category $category */
            $category = $this->_categoryFactory->create()->load($parentId);
            if (!empty($category)) {
                $tree = $this->_getNodesArray($this->getNode($category, $recursionLevel));
                if (!empty($tree) && !empty($tree['children'])) {
                    $result = $tree['children'];
                }
            }
        }
        else {
            $result = $this->_getNodesArray($this->getRoot(null, $recursionLevel));
        }
        if ($asJson) {
            return $this->_coreData->jsonEncode($result);
        }
        return $result;
    }

    /**
     * Get categories collection
     *
     * @return Magento_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        $collection = $this->_getData('category_collection');
        if (is_null($collection)) {
            $collection = $this->_categoryFactory->create()->getCollection()
                ->addAttributeToSelect(array('name', 'is_active'))
                ->setLoadProductCount(true)
            ;
            $this->setData('category_collection', $collection);
        }
        return $collection;
    }

    /**
     * Convert categories tree to array recursively
     *
     * @return array
     */
    protected function _getNodesArray($node)
    {
        $eventHelper = $this->helper('Magento_CatalogEvent_Helper_Adminhtml_Event');
        $result = array(
            'id'             => (int)$node->getId(),
            'parent_id'      => (int)$node->getParentId(),
            'children_count' => (int)$node->getChildrenCount(),
            'is_active'      => (bool)$node->getIsActive(),
            'disabled'       => ($node->getLevel() <= 1 || in_array(
                                    $node->getId(),
                                    $eventHelper->getInEventCategoryIds()
                                )),
            'name'           => $node->getName(),
            'level'          => (int)$node->getLevel(),
            'product_count'  => (int)$node->getProductCount(),
        );
        if ($node->hasChildren()) {
            $result['children'] = array();
            foreach ($node->getChildren() as $childNode) {
                $result['children'][] = $this->_getNodesArray($childNode);
            }
        }
        $result['cls'] = ($result['is_active'] ? '' : 'no-') . 'active-category';
        if ($result['disabled']) {
            $result['cls'] .= ' em';
        }
        $result['expanded'] = false;
        if (!empty($result['children'])) {
            $result['expanded'] = true;
        }
        return $result;
    }

    /**
     * Get URL for categories tree ajax loader
     *
     * @return string
     */
    public function getLoadTreeUrl()
    {
        return $this->getUrl('*/*/categoriesJson');
    }
}
