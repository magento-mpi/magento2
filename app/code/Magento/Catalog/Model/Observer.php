<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class Observer
{
    /**
     * @var Indexer\Category\Flat\State
     */
    protected $categoryFlatConfig;

    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData;

    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_catalogCategory;

    /**
     * Index indexer
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexIndexer;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * Store manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Model\Resource\Product
     */
    protected $_catalogProduct;

    /**
     * Catalog category1
     *
     * @var \Magento\Catalog\Model\Resource\Category
     */
    protected $_categoryResource;

    /**
     * Url factory
     *
     * @var \Magento\Catalog\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * Factory for product resource
     *
     * @var \Magento\Catalog\Model\Resource\ProductFactory
     */
    protected $_productResourceFactory;

    /**
     * @param \Magento\Catalog\Model\UrlFactory $urlFactory
     * @param \Magento\Catalog\Model\Resource\Category $categoryResource
     * @param \Magento\Catalog\Model\Resource\Product $catalogProduct
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Category $catalogLayer
     * @param \Magento\Index\Model\Indexer $indexIndexer
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param Indexer\Category\Flat\State $categoryFlatState
     * @param \Magento\Catalog\Model\Resource\ProductFactory $productResourceFactory
     */
    public function __construct(
        \Magento\Catalog\Model\UrlFactory $urlFactory,
        \Magento\Catalog\Model\Resource\Category $categoryResource,
        \Magento\Catalog\Model\Resource\Product $catalogProduct,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Category $catalogLayer,
        \Magento\Index\Model\Indexer $indexIndexer,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Catalog\Model\Resource\ProductFactory $productResourceFactory
    ) {
        $this->_urlFactory = $urlFactory;
        $this->_categoryResource = $categoryResource;
        $this->_catalogProduct = $catalogProduct;
        $this->_storeManager = $storeManager;
        $this->_catalogLayer = $catalogLayer;
        $this->_indexIndexer = $indexIndexer;
        $this->_catalogCategory = $catalogCategory;
        $this->_catalogData = $catalogData;
        $this->categoryFlatConfig = $categoryFlatState;
        $this->_productResourceFactory = $productResourceFactory;
    }

    /**
     * Checking whether the using static urls in WYSIWYG allowed event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function catalogCheckIsUsingStaticUrlsAllowed(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getEvent()->getData('store_id');
        $result = $observer->getEvent()->getData('result');
        $result->isAllowed = $this->_catalogData->setStoreId($storeId)->isUsingStaticUrlsAllowed();
    }

    /**
     * Adds catalog categories to top menu
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function addCatalogToTopmenuItems(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->addIdentity(\Magento\Catalog\Model\Category::CACHE_TAG);
        $this->_addCategoriesToMenu($this->_catalogCategory->getStoreCategories(), $observer->getMenu(), $block);
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param \Magento\Framework\Data\Tree\Node\Collection|array $categories
     * @param \Magento\Framework\Data\Tree\Node $parentCategoryNode
     * @param \Magento\Theme\Block\Html\Topmenu $block
     * @return void
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $block)
    {
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }

            $nodeId = 'category-node-' . $category->getId();

            $block->addIdentity(\Magento\Catalog\Model\Category::CACHE_TAG . '_' . $category->getId());

            $tree = $parentCategoryNode->getTree();
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => $this->_catalogCategory->getCategoryUrl($category),
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            $categoryNode = new \Magento\Framework\Data\Tree\Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);

            if ($this->categoryFlatConfig->isFlatEnabled()) {
                $subcategories = (array)$category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }

            $this->_addCategoriesToMenu($subcategories, $categoryNode, $block);
        }
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param \Magento\Framework\Data\Tree\Node $category
     * @return bool
     */
    protected function _isActiveMenuCategory($category)
    {
        if (!$this->_catalogLayer) {
            return false;
        }

        $currentCategory = $this->_catalogLayer->getCurrentCategory();
        if (!$currentCategory) {
            return false;
        }

        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }
}
