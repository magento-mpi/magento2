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

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Resource\Category\Tree;
use Magento\CatalogEvent\Model\Resource\Event\Collection;
use Magento\CatalogEvent\Model\Resource\Event\CollectionFactory;
use Magento\Data\Tree\Node;
use Magento\Data\Tree\Node\Collection as NodeCollection;

class Event extends AbstractHelper
{
    /**
     * Categories first and second level for admin
     *
     * @var NodeCollection
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
     * @var CollectionFactory
     */
    protected $_eventCollectionFactory;

    /**
     * Category model factory
     *
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Construct
     *
     * @param Context $context
     * @param CollectionFactory $factory
     * @param CategoryFactory $categoryFactory
     */
    public function __construct(Context $context, CollectionFactory $factory, CategoryFactory $categoryFactory)
    {
        parent::__construct($context);

        $this->_eventCollectionFactory = $factory;
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Return first and second level categories
     *
     * @return NodeCollection
     */
    public function getCategories()
    {
        if ($this->_categories === null) {
            /** @var $tree Tree */
            $tree = $this->_categoryFactory->create()->getTreeModel();
            $tree->load(null, 2);
            // Load only to second level.
            $tree->addCollectionData(null, 'position');
            $this->_categories = $tree->getNodeById(Category::TREE_ROOT_ID)->getChildren();
        }
        return $this->_categories;
    }

    /**
     * Return first and second level categories for dropdown options
     *
     * @param array $without
     * @param bool $emptyOption
     * @return array
     */
    public function getCategoriesOptions($without = array(), $emptyOption = false)
    {
        $result = array();
        foreach ($this->getCategories() as $category) {
            if (!in_array($category->getId(), $without)) {
                $result[] = $this->_treeNodeToOption($category, $without);
            }
        }

        if ($emptyOption) {
            array_unshift($result, array('label' => '', 'value' => ''));
        }
        return $result;
    }

    /**
     * Convert tree node to dropdown option
     *
     * @param Node $node
     * @param array $without
     * @return array
     */
    protected function _treeNodeToOption(Node $node, $without)
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
     * @param NodeCollection $categories
     * @param int $categoryId
     * @return Node|false
     */
    public function searchInCategories($categories, $categoryId)
    {
        foreach ($categories as $category) {
            if ($category->getId() == $categoryId) {
                return $category;
            } elseif ($category->hasChildren() && ($foundCategory = $this->searchInCategories(
                $category->getChildren(),
                $categoryId
            ))
            ) {
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
            /** @var Collection $collection */
            $collection = $this->_eventCollectionFactory->create();
            $this->_inEventCategoryIds = $collection->getColumnValues('category_id');
        }
        return $this->_inEventCategoryIds;
    }
}
