<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event adminhtml data helper
 */
namespace Magento\CatalogEvent\Helper\Adminhtml;

class Event extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Categories first and second level for admin
     *
     * @var \Magento\Data\Tree\Node
     */
    protected $_categories = null;

    /**
     * List of category ids that already in events
     *
     * @var array
     */
    protected $_inEventCategoryIds = null;

    /**
     * Event collection factory
     *
     * @var Magento_CatalogEvent_Model_Resource_Event_CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * Category model factory
     *
     * @var Magento_Catalog_Model_CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $factory
     * @param Magento_Catalog_Model_CategoryFactory $categoryFactory
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_CatalogEvent_Model_Resource_Event_CollectionFactory $factory,
        Magento_Catalog_Model_CategoryFactory $categoryFactory
    ) {
        parent::__construct($context);

        $this->_eventCollectionFactory = $factory;
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Return first and second level categories
     *
     * @return \Magento\Data\Tree\Node
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            /** @var $tree Magento_Catalog_Model_Resource_Category_Tree */
            $tree = $this->_categoryFactory->create()->getTreeModel();
            $tree->load(null, 2); // Load only to second level.
            $tree->addCollectionData(null, 'position');
            $this->_categories = $tree->getNodeById(\Magento\Catalog\Model\Category::TREE_ROOT_ID)->getChildren();
        }
        return $this->_categories;
    }

    /**
     * Return first and second level categories for dropdown options
     *
     * @return array
     */
    public function getCategoriesOptions($without = array(), $emptyOption = false)
    {
        $result = array();
        foreach ($this->getCategories() as $category) {
            if (! in_array($category->getId(), $without)) {
                $result[] = $this->_treeNodeToOption($category, $without);
            }
        }

        if ($emptyOption) {
            array_unshift($result, array(
                'label' => '' , 'value' => ''
            ));
        }
        return $result;
    }

    /**
     * Convert tree node to dropdown option
     *
     * @return array
     */
    protected function _treeNodeToOption(\Magento\Data\Tree\Node $node, $without)
    {

        $option = array();
        $option['label'] = $node->getName();
        if ($node->getLevel() < 2) {
            $option['value'] = array();
            foreach ($node->getChildren() as $childNode) {
                if (!in_array($childNode->getId(), $without)) {
                    $option['value'][] = $this->_treeNodeToOption($childNode, $without);
                }
            }
        } else {
            $option['value'] = $node->getId();
        }
        return $option;
    }

    /**
     * Search category in categories tree
     *
     * @param array $categories
     * @param int $categoryId
     * @return \Magento\Data\Tree\Node|boolean
     */
    public function searchInCategories($categories, $categoryId)
    {

        foreach ($categories as $category) {
            if ($category->getId() == $categoryId) {
                return $category;
            } elseif ($category->hasChildren() && ($foundCategory = $this->searchInCategories($category->getChildren(), $categoryId))) {
                return $foundCategory;
            }
        }
        return false;
    }

    /**
     * Return list of category ids that already in events
     *
     * @return array
     */
    public function getInEventCategoryIds()
    {

        if ($this->_inEventCategoryIds === null) {
            /** @var Magento_CatalogEvent_Model_Resource_Event_Collection $collection */
            $collection = $this->_eventCollectionFactory->create();
            $this->_inEventCategoryIds = $collection->getColumnValues('category_id');
        }
        return $this->_inEventCategoryIds;
    }
}
