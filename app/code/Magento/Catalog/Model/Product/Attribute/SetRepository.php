<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

class SetRepository implements \Magento\Catalog\Api\AttributeSetRepositoryInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaDataBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository
     * @param \Magento\Framework\Api\SearchCriteriaDataBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Framework\Api\SearchCriteriaDataBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet)
    {
        return $this->attributeSetRepository->save($attributeSet);
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('entity_type_code')
                    ->setValue(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
                    ->setConditionType('eq')
                    ->create()
            ]
        );
        $this->searchCriteriaBuilder->setCurrentPage($searchCriteria->getCurrentPage());
        $this->searchCriteriaBuilder->setPageSize($searchCriteria->getPageSize());
        return $this->attributeSetRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeSetId)
    {
        return $this->attributeSetRepository->get($attributeSetId);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Eav\Api\Data\AttributeSetInterface $attributeSet)
    {
        return $this->attributeSetRepository->delete($attributeSet);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($attributeSetId)
    {
        return $this->attributeSetRepository->deleteById($attributeSetId);
    }
}
