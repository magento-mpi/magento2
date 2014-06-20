<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data\Category;

use Magento\Framework\Data\Tree\Node;

/**
 * Retrieve category data represented in tree structure
 */
class Tree
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\Tree
     */
    protected $categoryTree;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Eav\Category\TreeBuilderFactory
     */
    protected $treeBuilderFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Service\V1\Data\Eav\Category\TreeBuilderFactory $treeBuilderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\Tree $categoryTree,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Service\V1\Data\Eav\Category\TreeBuilderFactory $treeBuilderFactory
    ) {
        $this->categoryTree = $categoryTree;
        $this->storeManager = $storeManager;
        $this->categoryFactory = $categoryFactory;
        $this->treeBuilderFactory = $treeBuilderFactory;
    }

    /**
     * @param \Magento\Catalog\Model\Category|null $category
     * @return Node|null
     */
    public function getRootNode($category = null)
    {
        if (!is_null($category) && $category->getId()) {
            return $this->getNode($category);
        }

        $store = $this->storeManager->getStore();
        $rootId = $store->getRootCategoryId();

        $tree = $this->categoryTree->load(null);
        $tree->addCollectionData($this->getCategoryCollection());
        $root = $tree->getNodeById($rootId);
        return $root;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return Node
     */
    protected function getNode(\Magento\Catalog\Model\Category $category)
    {
        $nodeId = $category->getId();
        $node = $this->categoryTree->loadNode($nodeId);
        $node->loadChildren();
        $this->categoryTree->addCollectionData($this->getCategoryCollection());
        return $node;
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function getCategoryCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->categoryFactory->create()->getCollection();

        $collection->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'is_active'
        )->setProductStoreId(
            $storeId
        )->setLoadProductCount(
            true
        )->setStoreId(
            $storeId
        );

        return $collection;
    }

    /**
     * @param Magento\Framework\Data\Tree\Node $node
     * @param int $depth
     * @param int $currentLevel
     * @return \Magento\Catalog\Service\V1\Data\Eav\Category\Tree[]
     */
    public function getTree($node, $depth = 0, $currentLevel = 0)
    {
        $builder = $this->treeBuilderFactory->create();
        $builder->setId($node->getId())
            ->setParentId($node->getParentId())
            ->setName($node->getName())
            ->setPosition($node->getPosition())
            ->setLevel($node->getLevel())
            ->setActive($node->getIsActive())
            ->setChildren([]);

        if ($node->hasChildren()) {
            $children = array();
            foreach ($node->getChildren() as $child) {
                if ($depth && $depth <= $currentLevel) {
                    break;
                }
                $children[] = $this->getTree($child, $depth, $currentLevel + 1);
            }
            $builder->setChildren($children);
        }
        return $builder->create();
    }
}
