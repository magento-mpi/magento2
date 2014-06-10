<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\AdvancedSearch;

use Magento\Catalog\Model\Layer\ItemCollectionProviderInterface;
use Magento\CatalogSearch\Model\Resource\Advanced\CollectionFactory;

class ItemCollectionProvider implements ItemCollectionProviderInterface
{
    /**
     * @var CollectionFactory
     */
    protected $advancedCollectionFactory;

    /**
     * ItemCollectionProvider constructor
     *
     * @param CollectionFactory $fulltextCollectionFactory
     */
    public function __construct(CollectionFactory $fulltextCollectionFactory)
    {
        $this->advancedCollectionFactory = $fulltextCollectionFactory;
    }

    /**
     * Retrieve item colleciton
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\CatalogSearch\Model\Resource\Advanced\Collection
     */
    public function getCollection(\Magento\Catalog\Model\Category $category)
    {
        return $this->advancedCollectionFactory->create();
    }
}
