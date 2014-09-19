<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Layer\Advanced;


class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
{
    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\CatalogSearch\Model\Layer\Advanced $layer
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\CatalogSearch\Model\Layer\Advanced $layer
    ) {
        parent::__construct($collectionFactory, $storeManager, $layer);
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\Collection $collection
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addDisplayInAdvancedSearchFilter()->addVisibleFilter();
        return $collection;
    }
}
