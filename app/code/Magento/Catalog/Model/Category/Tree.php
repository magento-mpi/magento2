<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category;

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
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Catalog\Api\Data\CategoryTreeBuilderFactory
     */
    protected $treeBuilderFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Category\Tree $categoryTree
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection
     * @param \Magento\Catalog\Api\Data\CategoryTreeBuilderFactory $treeBuilderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Category\Tree $categoryTree,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Resource\Category\Collection $categoryCollection,
        \Magento\Catalog\Api\Data\CategoryTreeBuilderFactory $treeBuilderFactory
    ) {
        $this->categoryTree = $categoryTree;
        $this->storeManager = $storeManager;
        $this->categoryCollection = $categoryCollection;
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
        $this->prepareCollection();
        $tree->addCollectionData($this->categoryCollection);
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
        $this->prepareCollection();
        $this->categoryTree->addCollectionData($this->categoryCollection);
        return $node;
    }

    /**
     * @return void
     */
    protected function prepareCollection()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->categoryCollection->addAttributeToSelect(
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
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param int $depth
     * @param int $currentLevel
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface[]
     */
    public function getTree($node, $depth = null, $currentLevel = 0)
    {
        $builder = $this->treeBuilderFactory->create();
        $builder->setId($node->getId())
            ->setParentId($node->getParentId())
            ->setName($node->getName())
            ->setPosition($node->getPosition())
            ->setLevel($node->getLevel())
            ->setActive($node->getIsActive())
            ->setProductCount($node->getProductCount())
            ->setChildren([]);

        if ($node->hasChildren()) {
            $children = array();
            foreach ($node->getChildren() as $child) {
                if (!is_null($depth) && $depth <= $currentLevel) {
                    break;
                }
                $children[] = $this->getTree($child, $depth, $currentLevel + 1);
            }
            $builder->setChildren($children);
        }
        return $builder->create();
    }
}
