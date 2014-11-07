<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Service\V1;

use Magento\Framework\Api\Config\MetadataConfig;
use Magento\Framework\Api\SimpleDataObjectConverter;

/**
 * Class RmaMetadataRead
 */
class RmaMetadataRead implements RmaMetadataReadInterface
{
    /**
     * List of item dto methods
     *
     * @var array
     */
    private $dataObjectMethods = [];

    /**
     * @var MetadataConfig
     */
    private $metadataConfig;

    /**
     * Metadata
     *
     * @var \Magento\Customer\Api\MetadataInterface
     */
    protected $metadata;

    /**
     * Constructor
     *
     * @param MetadataConfig $metadataConfig
     */
    public function __construct(MetadataConfig $metadataConfig, \Magento\Customer\Api\MetadataInterface $metadata)
    {
        $this->metadataConfig = $metadataConfig;
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        return $this->metadata->getAttributes($formCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($attributeCode)
    {
        return $this->metadata->getAttributeMetadata($attributeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributesMetadata()
    {
        return $this->metadata->getAllAttributesMetadata();
    }

    /**
     *  Get custom attribute metadata for the given Data object's attribute set
     *
     * @param string|null $dataObjectClassName Data object class name
     * @return \Magento\Framework\Api\MetadataObjectInterface[]
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME)
    {
        $customAttributes = [];
        if (!$this->dataObjectMethods) {
            $this->dataObjectMethods = array_flip(get_class_methods($dataObjectClassName));
        }
        foreach ($this->getAllAttributesMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            $camelCaseKey = SimpleDataObjectConverter::snakeCaseToUpperCamelCase($attributeCode);
            $isDataObjectMethod = isset($this->dataObjectMethods['get' . $camelCaseKey])
                || isset($this->dataObjectMethods['is' . $camelCaseKey]);

            /** Even though disable_auto_group_change is system attribute, it should be available to the clients */
            if (!$isDataObjectMethod
                && (!$attributeMetadata->isSystem() || $attributeCode == 'disable_auto_group_change')
            ) {
                $customAttributes[] = $attributeMetadata;
            }
        }

        return array_merge($customAttributes, $this->metadataConfig->getCustomAttributesMetadata($dataObjectClassName));
    }
}
