<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

use \Magento\Eav\Api\AttributeSetRepositoryInterface;
use \Magento\Eav\Api\Data\AttributeSetInterface;
use \Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use \Magento\Eav\Model\Resource\Entity\Attribute\Set as AttributeSetResource;
use \Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use \Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory;
use \Magento\Eav\Model\Config as EavConfig;
use \Magento\Framework\Exception\NoSuchEntityException;
use \Magento\Framework\Data\Search\SearchCriteriaInterface;
use \Magento\Framework\Data\Search\SearchResultsBuilder;

class AttributeSetRepository implements AttributeSetRepositoryInterface
{
    /**
     * @var AttributeSetResource
     */
    private $attributeSetResource;

    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var SearchResultsBuilder
     */
    private $searchResultsBuilder;

    /**
     * @param AttributeSetResource $attributeSetResource
     * @param AttributeSetFactory $attributeSetFactory
     * @param CollectionFactory $collectionFactory
     * @param Config $eavConfig
     * @param SearchResultsBuilder $searchResultBuilder
     */
    public function __construct(
        AttributeSetResource $attributeSetResource,
        AttributeSetFactory $attributeSetFactory,
        CollectionFactory $collectionFactory,
        EavConfig $eavConfig,
        SearchResultsBuilder $searchResultBuilder
    ) {
        $this->attributeSetResource = $attributeSetResource;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->collectionFactory = $collectionFactory;
        $this->eavConfig = $eavConfig;
        $this->searchResultBuilder = $searchResultBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(AttributeSetInterface $attributeSet, array $arguments = [])
    {
        $this->attributeSetResource->save($attributeSet);
        return $attributeSet;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria, array $arguments = [])
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Set\Collection $collection */
        $collection = $this->collectionFactory->create();

        /**
         * The only possible/meaningful search criteria for attribute set is entity type code
         */
        $entityTypeCode = $this->getEntityTypeCode($searchCriteria);

        if (!is_null($entityTypeCode)) {
            $collection->setEntityTypeFilter($this->eavConfig->getEntityType($entityTypeCode)->getId());
        }

        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $totalCount = $collection->getSize();

        $this->searchResultsBuilder->setItems($collection->getItems());
        $this->searchResultsBuilder->setTotalCount($totalCount);
        return $this->searchResultsBuilder->create();
    }

    /**
     * Retrieve entity type code from search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return null|string
     */
    protected function getEntityTypeCode(SearchCriteriaInterface $searchCriteria)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() == 'entity_type_code') {
                    return $filter->getValue();
                }
            }
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeSetId, array $arguments = [])
    {
        /** @var AttributeSet $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $this->attributeSetResource->load($attributeSet, $attributeSetId);

        if (!$attributeSet->getId()) {
            throw NoSuchEntityException::singleField('id', $attributeSetId);
        }
        return $attributeSet;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AttributeSetInterface $attributeSet, array $arguments = [])
    {
        /**
         * @todo default attribute set must not be deleted. Corresponding logic have to be moved to resource model.
         */
        $this->attributeSetResource->delete($attributeSet);
        return true;
    }
}
