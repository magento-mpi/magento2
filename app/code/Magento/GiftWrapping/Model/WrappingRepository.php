<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

use \Magento\Framework\Exception\NoSuchEntityException;

class WrappingRepository
{
    /**
     * @var \Magento\GiftWrapping\Model\WrappingFactory
     */
    protected $wrappingFactory;

    /**
     * @var \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory
     */
    protected $wrappingCollectionFactory;

    /**
     * @param \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory
     * @param \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
     */
    public function __construct(
        \Magento\GiftWrapping\Model\WrappingFactory $wrappingFactory,
        \Magento\GiftWrapping\Model\Resource\Wrapping\CollectionFactory $wrappingCollectionFactory
    ) {
        $this->wrappingFactory = $wrappingFactory;
        $this->wrappingCollectionFactory = $wrappingCollectionFactory;
    }

    /**
     * Load wrapping model for specified store
     *
     * @param int $id
     * @param int $storeId
     * @return \Magento\GiftWrapping\Model\Wrapping
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id, $storeId = null)
    {
        $wrapping = $this->wrappingFactory->create();
        $wrapping->setStoreId($storeId);
        $wrapping->load($id);
        if (!$wrapping->getId()) {
            throw new NoSuchEntityException('Gift Wrapping with specified ID "%1" not found.', [$id]);
        }
        return $wrapping;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Data\SearchCriteria  $criteria
     * @return \Magento\GiftWrapping\Model\Wrapping[]
     */
    public function find(\Magento\Framework\Data\SearchCriteria $criteria)
    {
        $collection = $this->wrappingCollectionFactory->create();
        $collection->addWebsitesToResult();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'status' && $filter->getValue()) {
                    $collection->applyStatusFilter();
                } elseif ($filter->getField() == 'store_id') {
                    $collection->addStoreAttributesToResult((int)$filter->getValue());
                } else {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
                }
            }
        }
        return $collection->getItems();
    }
}
