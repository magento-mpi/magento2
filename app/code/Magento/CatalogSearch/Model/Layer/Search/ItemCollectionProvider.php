<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Search;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\CatalogSearch\Model\Resource\Fulltext\CollectionFactory;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var \Magento\CatalogSearch\Model\Resource\Fulltext\CollectionFactory
     */
    protected $fulltextCollectionFactory;

    /**
     * ItemCollectionProvider constructor
     *
     * @param CollectionFactory $fulltextCollectionFactory
     */
    public function __construct(CollectionFactory $fulltextCollectionFactory)
    {
        $this->fulltextCollectionFactory = $fulltextCollectionFactory;
    }

    /**
     * Retrieve item colleciton
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\Catalog\Model\Resource\Product\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        return $this->fulltextCollectionFactory->create();
    }
}
