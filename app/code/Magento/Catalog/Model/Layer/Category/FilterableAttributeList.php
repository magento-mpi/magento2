<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Category;


use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterableAttributeList implements FilterableAttributeListInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer\Category
     */
    protected $layer;

    /**
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->layer = $layer;
    }

    /**
     * Retrieve list of filterable attributes
     *
     * @return array|\Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getList()
    {
        $setIds = $this->layer->getProductCollection()->getSetIds();
        if (!$setIds) {
            return array();
        }
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = $this->collectionFactory->create();
        $collection->setItemObjectClass('Magento\Catalog\Model\Resource\Eav\Attribute')
            ->setAttributeSetFilter($setIds)
            ->addStoreLabel($this->storeManager->getStore()->getId())
            ->setOrder('position', 'ASC');
        $collection = $this->_prepareAttributeCollection($collection);
        $collection->load();

        return $collection;
    }

    /**
     * Add filters to attribute collection
     *
     * @param   \Magento\Catalog\Model\Resource\Product\Attribute\Collection $collection
     * @return  \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        $collection->addIsFilterableFilter();
        return $collection;
    }
} 
