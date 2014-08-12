<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Cached Customer attribute metadata service
 */
class CustomerMetadataServiceCached implements CustomerMetadataServiceInterface
{
    /**
     * @var CustomerMetadataService
     */
    private $metadataService;

    /**
     * @var array
     */
    private $attributeMetadataCache = [];

    /**
     * @var array
     */
    private $attributesCache = [];

    /**
     * @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    private $allAttributeMetadataCache = null;

    /**
     * @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    private $customAttributesMetadataCache = null;

    const CACHE_SEPARATOR = ';';

    /**
     * @param CustomerMetadataServiceInterface $metadataService
     */
    public function __construct(
        CustomerMetadataServiceInterface $metadataService
    ) {
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $key = $formCode;
        if (isset($this->attributesCache[$key])) {
            return $this->attributesCache[$key];
        }

        $value = $this->metadataService->getAttributes($formCode);
        $this->attributesCache[$key] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($attributeCode)
    {
        $key = $attributeCode;
        if (isset($this->attributeMetadataCache[$key])) {
            return $this->attributeMetadataCache[$key];
        }

        $value = $this->metadataService->getAttributeMetadata($attributeCode);
        $this->attributeMetadataCache[$key] = $value;

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributesMetadata()
    {
        if (!is_null($this->allAttributeMetadataCache)) {
            return $this->allAttributeMetadataCache;
        }

        $this->allAttributeMetadataCache = $this->metadataService->getAllAttributesMetadata();
        return $this->allAttributeMetadataCache;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttributesMetadata()
    {
        if (!is_null($this->customAttributesMetadataCache)) {
            return $this->customAttributesMetadataCache;
        }

        $this->customAttributesMetadataCache = $this->metadataService->getCustomAttributesMetadata();
        return $this->customAttributesMetadataCache;
    }
}
