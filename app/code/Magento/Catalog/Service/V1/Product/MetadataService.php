<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;

/**
 * Class AttributeMetadataService
 */
class MetadataService implements MetadataServiceInterface
{
    /** @var  \Magento\Catalog\Service\V1\MetadataService */
    protected $metadataService;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    private $filterBuilder;

    /**
     * @param \Magento\Catalog\Service\V1\MetadataService $metadataService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Catalog\Service\V1\MetadataService $metadataService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
    ) {
        $this->metadataService = $metadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Retrieve custom EAV attribute metadata of product
     *
     * @param int $attributeSetId
     * @return AttributeMetadata[]
     */
    public function getCustomAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID)
    {
        $customAttributes = [];
        foreach ($this->getProductAttributesMetadata($attributeSetId) as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return $customAttributes;
    }

    /**
     * Retrieve EAV attribute metadata of product
     *
     * @param int $attributeSetId
     * @return AttributeMetadata[]
     */
    public function getProductAttributesMetadata($attributeSetId = self::DEFAULT_ATTRIBUTE_SET_ID)
    {
        /** @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteria */
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
