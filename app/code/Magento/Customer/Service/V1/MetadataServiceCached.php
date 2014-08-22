<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Cached attribute metadata service
 */
class MetadataServiceCached implements MetadataServiceInterface
{
    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * @var array
     */
    protected $attributeMetadataCache = [];

    /**
     * @var array
     */
    protected $attributesCache = [];

    /**
     * @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    protected $allAttributeMetadataCache = null;

    /**
     * @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    protected $customAttributesMetadataCache = null;

    const CACHE_SEPARATOR = ';';

    /**
     * Initialize dependencies.
     *
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(MetadataServiceInterface $metadataService)
    {
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
    public function getCustomAttributesMetadata($dataObjectClassName = null)
    {
        if (!is_null($this->customAttributesMetadataCache)) {
            return $this->customAttributesMetadataCache;
        }

        $this->customAttributesMetadataCache = $this->metadataService->getCustomAttributesMetadata();
        return $this->customAttributesMetadataCache;
    }
}
