<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute;

class Repository implements \Magento\Catalog\Api\ProductAttributeRepositoryInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected $attributeResource;

    /**
     * @var \Magento\Framework\Data\Search\SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $eavAttributeRepository;

    /**
     * @var \Magento\Framework\Data\Search\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Data\Search\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\IdentifierFactory
     */
    protected $attributeIdentifierFactory;

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attributeResource
     * @param \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder
     * @param \Magento\Framework\Data\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Data\Search\FilterBuilder $filterBuilder
     * @param \Magento\Eav\Model\AttributeRepository $eavAttributeRepository
     * @param \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Eav\Attribute $attributeResource,
        \Magento\Framework\Data\Search\SearchResultsBuilder $searchResultsBuilder,
        \Magento\Framework\Data\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Data\Search\FilterBuilder $filterBuilder,
        \Magento\Eav\Model\AttributeRepository $eavAttributeRepository,
        \Magento\Eav\Model\Entity\Attribute\IdentifierFactory $attributeIdentifierFactory
    ) {
        $this->attributeResource = $attributeResource;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->eavAttributeRepository = $eavAttributeRepository;
        $this->attributeIdentifierFactory = $attributeIdentifierFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode, array $arguments = [])
    {
        $identifier = $this->attributeIdentifierFactory->create([
            'attributeCode' => $attributeCode,
            'entityTypeCode' => \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE
        ]);
        return $this->eavAttributeRepository->get($identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Data\Search\SearchCriteriaInterface $searchCriteria,
        array $arguments = []
    ) {
        $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('entityTypeCode')
                    ->setValue(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                    ->create()
            ]
        );

        $this->searchCriteriaBuilder->setFilterGroups($searchCriteria->getFilterGroups());
        $this->searchCriteriaBuilder->setSortOrders($searchCriteria->getSortOrders());
        $this->searchCriteriaBuilder->setPageSize($searchCriteria->getPageSize());
        $this->searchCriteriaBuilder->setCurrentPage($searchCriteria->getCurrentPage());

        return $this->eavAttributeRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = [])
    {
        /**
         * todo: merge create and update logic in this method (validation etc.)
         */
        $this->attributeResource->save($attribute);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Catalog\Api\Data\ProductAttributeInterface $attribute, array $arguments = [])
    {
        $this->attributeResource->delete($attribute);
        return true;
    }
}
