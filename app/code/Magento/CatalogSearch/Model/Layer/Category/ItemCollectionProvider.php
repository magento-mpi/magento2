<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * Collection name to create
     *
     * @var string
     */
    protected $collectionName;

    /**
     * Layer Search constructor
     *
     *  @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManager $objectManager,
        $collectionName = '\Magento\CatalogSearch\Model\Resource\Fulltext\Collection'
    ) {
        $this->objectManager = $objectManager;
        $this->collectionName = $collectionName;
    }

    /**
     * Get item collection
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\CatalogSearch\Model\Resource\Fulltext\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        $collection = $this->objectManager->create($this->collectionName);
        $collection->setStoreId($category->getStoreId())
            ->addCategoryFilter($category);
        return $collection;
    }
}
