<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category;

use Magento\Catalog\Api\CategoryAttributeRepositoryInterface;

class AttributeRepository implements CategoryAttributeRepositoryInterface
{
    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    protected $eavAttributeRepository;

    /**
     * @param \Magento\Framework\Service\Config\MetadataConfig $metadataConfig
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository
     */
    public function __construct(
        \Magento\Framework\Service\Config\MetadataConfig $metadataConfig,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Eav\Api\AttributeRepositoryInterface $eavAttributeRepository
    ) {
        $this->metadataConfig = $metadataConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->eavAttributeRepository = $eavAttributeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
    {
        return $this->eavAttributeRepository->getList(
            \Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE,
            $searchCriteria
        );
    }

    /**
     * {@inheritdoc}
     */
    public function get($attributeCode)
    {
        return $this->eavAttributeRepository->get(
            \Magento\Catalog\Api\Data\CategoryAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            [
                $this->filterBuilder
                    ->setField('attribute_set_id')
                    ->setValue(\Magento\Catalog\Api\Data\CategoryAttributeInterface::DEFAULT_ATTRIBUTE_SET_ID)
                    ->create()
            ]
        );

        $customAttributes = [];
        $entityAttributes = $this->getList($searchCriteria->create())->getItems();

        foreach ($entityAttributes as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return array_merge($customAttributes, $this->metadataConfig->getCustomAttributesMetadata($dataObjectClassName));
    }
}
