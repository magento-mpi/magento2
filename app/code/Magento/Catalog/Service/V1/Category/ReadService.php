<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

/**
 * Class ReadService
 *
 * @package Magento\Catalog\Service\V1\Category
 */
class ReadService implements ReadServiceInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Service\V1\Data\Category\Tree
     */
    protected $categoryTree;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Service\V1\Data\Category\Tree $categoryTree
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Service\V1\Data\Category\Tree $categoryTree,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryTree = $categoryTree;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function tree($rootCategoryId = null, $depth = 0)
    {
        $category = null;
        if (!is_null($rootCategoryId)) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryFactory->create()->load($rootCategoryId);
            if (!$category->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException('Root Category does not exist');
            }
            $storeRootId = $this->storeManager->getStore()->getRootCategoryId();
            if (!in_array($storeRootId, $category->getPathIds())) {
                throw new \Magento\Framework\Exception\InputException('Category does not belong to current store');
            }
        }
        $result = $this->categoryTree->getTree($this->categoryTree->getRootNode($category), $depth);
        return $result;
    }
}
