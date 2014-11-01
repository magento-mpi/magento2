<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Category;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Framework\Service\Config\MetadataConfig;

/**
 * Class AttributeMetadataService
 */
class MetadataService implements MetadataServiceInterface
{
    /** @var  \Magento\Catalog\Service\V1\MetadataServiceInterface */
    protected $metadataService;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var MetadataConfig
     */
    private $metadataConfig;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param MetadataConfig $metadataConfig
     */
    public function __construct(
        \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        MetadataConfig $metadataConfig
    ) {
        $this->metadataService = $metadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->metadataConfig = $metadataConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME)
    {
        $customAttributes = [];
        foreach ($this->getCategoryAttributesMetadata(
            MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID
        ) as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return array_merge($customAttributes, $this->metadataConfig->getCustomAttributesMetadata($dataObjectClassName));
    }

    /**
     * Retrieve EAV attribute metadata of category
     *
     * @param int $attributeSetId
     * @return AttributeMetadata[]
     */
    public function getCategoryAttributesMetadata($attributeSetId = MetadataServiceInterface::DEFAULT_ATTRIBUTE_SET_ID)
    {
        $this->searchCriteriaBuilder->addFilter([
            $this->filterBuilder
                ->setField('attribute_set_id')
                ->setValue($attributeSetId)
                ->create()
        ]);

        return $this->metadataService->getAllAttributeMetadata(
            MetadataServiceInterface::ENTITY_TYPE,
            $this->searchCriteriaBuilder->create()
        )->getItems();
    }
}
