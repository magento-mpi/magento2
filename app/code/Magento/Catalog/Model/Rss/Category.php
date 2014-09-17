<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Rss;

/**
 * Class Category
 * @package Magento\Catalog\Model\Rss
 */
class Category
{
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $visibility;

    /**
     * @param \Magento\Catalog\Model\Layer\Category $catalogLayer
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Category $catalogLayer,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility
    ) {
        $this->catalogLayer = $catalogLayer;
        $this->collectionFactory = $collectionFactory;
        $this->visibility = $visibility;
    }


    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param $storeId
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getProductCollection(\Magento\Catalog\Model\Category $category, $storeId)
    {
        /** @var $layer \Magento\Catalog\Model\Layer */
        $layer = $this->catalogLayer->setStore($storeId);
        $collection = $category->getResourceCollection();
        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($category->getChildren())
            ->load();
        /** @var $productCollection \Magento\Catalog\Model\Resource\Product\Collection */
        $productCollection = $this->collectionFactory->create();

        $currentCategory = $layer->setCurrentCategory($category);
        $layer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($collection);

        $category->getProductCollection()->setStoreId($storeId);

        $products = $currentCategory->getProductCollection()
            ->addAttributeToSort('updated_at', 'desc')
            ->setVisibility($this->visibility->getVisibleInCatalogIds())
            ->setCurPage(1)
            ->setPageSize(50);

        return $products;
    }
}
