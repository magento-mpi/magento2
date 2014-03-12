<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Layer\Category;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $engineProvider;

    public function __construct(
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
    ) {
        $this->engineProvider = $engineProvider;
    }

    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        $collection = $this->engineProvider->get()->getResultCollection();
        $collection->setStoreId($category->getStoreId())
            ->addCategoryFilter($category)
            ->setGeneralDefaultQuery();
        return $collection;
    }
} 
