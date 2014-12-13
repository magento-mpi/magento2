<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    /**
     * ItemCollectionProvider constructor
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
        $collection->setStoreId($category->getStoreId())
            ->addCategoryFilter($category)
            ->setGeneralDefaultQuery();
        return $collection;
    }
}
