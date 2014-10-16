<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Search;

class ItemCollectionProvider
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    /**
     * Layer Search constructor
     *
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     */
    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
    ) {
        $this->engineProvider = $engineProvider;
    }

    /**
     * Get item collection
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        $collection = $this->engineProvider->get()->getResultCollection();
        $collection->setStoreId($category->getStoreId());
        return $collection;
    }
}
