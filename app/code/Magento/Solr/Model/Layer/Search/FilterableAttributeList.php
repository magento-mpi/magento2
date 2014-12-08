<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Layer\Search;

class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Search\FilterableAttributeList
{
    /**
     * @var \Magento\Solr\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Solr\Helper\Data $helper
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Solr\Helper\Data $helper
    ) {
        $this->helper = $helper;
        parent::__construct($collectionFactory, $storeManager, $layerResolver);
    }

    /**
     * Get collection of all filterable attributes for layer products set
     *
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getList()
    {
        $setIds = $this->layer->getProductCollection()->getSetIds();
        if (!$setIds) {
            return [];
        }
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->collectionFactory->create()
            ->setItemObjectClass('Magento\Catalog\Model\Resource\Eav\Attribute');

        if ($this->helper->getTaxInfluence()) {
            $collection->removePriceFilter();
        }

        $collection
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel($this->storeManager->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }
}
