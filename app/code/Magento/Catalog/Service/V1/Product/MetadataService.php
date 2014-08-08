<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product;

use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Framework\Service\Config\Reader as ServiceConfigReader;
use Magento\Catalog\Service\V1\Data\Eav\AttributeMetadataBuilder;

/**
 * Class AttributeMetadataService
 */
class MetadataService implements MetadataServiceInterface
{
    /** @var \Magento\Catalog\Service\V1\MetadataService */
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
     * @var ServiceConfigReader
     */
    private $serviceConfigReader;

    /**
     * @var AttributeMetadataBuilder
     */
    private $attributeMetadataBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param ServiceConfigReader $serviceConfigReader
     * @param AttributeMetadataBuilder $attributeMetadataBuilder
     */
    public function __construct(
        \Magento\Catalog\Service\V1\MetadataServiceInterface $metadataService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        ServiceConfigReader $serviceConfigReader,
        AttributeMetadataBuilder $attributeMetadataBuilder
    ) {
        $this->metadataService = $metadataService;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->serviceConfigReader = $serviceConfigReader;
        $this->attributeMetadataBuilder = $attributeMetadataBuilder;
    }

    /**
     * Retrieve custom EAV attribute metadata of product
     *
     * @return AttributeMetadata[]
     */
    public function getCustomAttributesMetadata()
    {
        $customAttributes = [];
        foreach ($this->getProductAttributesMetadata(self::DEFAULT_ATTRIBUTE_SET_ID) as $attributeMetadata) {
            $customAttributes[] = $attributeMetadata;
        }
        return array_merge($customAttributes, $this->getAttributesFromConfig());
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

    /**
     * Retrieve attributes defined in a config.
     *
     * @return AttributeMetadata[]
     */
    protected function getAttributesFromConfig()
    {
        $attributes = [];
        $allAttributes = $this->serviceConfigReader->read();
        $dataObjectClass = 'Magento\Catalog\Service\V1\Data\Product';
        if (isset($allAttributes[$dataObjectClass]) && is_array($allAttributes[$dataObjectClass])) {
            foreach ($allAttributes[$dataObjectClass] as $attributeCode => $dataModel) {
                $this->attributeMetadataBuilder
                    ->setAttributeCode($attributeCode)
                    ->setBackendType($dataModel)
                    ->setFrontendInput('')
                    ->setValidationRules([])
                    ->setVisible(true)
                    ->setRequired(false)
                    ->setOptions([])
                    ->setFrontendClass('')
                    ->setFrontendLabel([])
                    ->setNote('')
                    ->setApplyTo('')
                    ->setFilterableInSearch(false)
                    ->setFilterable(false)
                    ->setAttributeId(0)
                    ->setVisibleOnFront(true)
                    ->setConfigurable(false)
                    ->setComparable(false)
                    ->setDefaultValue('')
                    ->setHtmlAllowedOnFront(false)
                    ->setNote('')
                    ->setPosition(0)
                    ->setWysiwygEnabled(false)
                    ->setVisibleInAdvancedSearch(false)
                    ->setUserDefined(false)
                    ->setSourceModel('')
                    ->setSystem(false)
                    ->setUnique(false)
                    ->setUsedForPromoRules(false);

                $attributes[$attributeCode] = $this->attributeMetadataBuilder->create();
            }
        }
        return $attributes;
    }
}
