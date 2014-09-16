<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Customer\Service\V1\Data\Eav\AttributeMetadataConverter;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Service\Config\MetadataConfig;

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
     * @var AttributeMetadataConverter
     */
    private $attributeMetadataConverter;

    /**
     * @var \Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @param MetadataConfig $metadataConfig
     * @param AttributeMetadataConverter $attributeMetadataConverter
     * @param \Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        MetadataConfig $metadataConfig,
        AttributeMetadataConverter $attributeMetadataConverter,
        \Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->metadataConfig = $metadataConfig;
        $this->attributeMetadataConverter = $attributeMetadataConverter;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($formCode)
    {
        $attributes = [];
        $attributesFormCollection = $this->attributeMetadataDataProvider->loadAttributesCollection(
            self::ENTITY_TYPE,
            $formCode
        );
        foreach ($attributesFormCollection as $attribute) {
            /** @var $attribute \Magento\Customer\Model\Attribute */
            $attributes[$attribute->getAttributeCode()] = $this->attributeMetadataConverter
                ->createMetadataAttribute($attribute);
        }
        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeMetadata($attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->attributeMetadataDataProvider->getAttribute(self::ENTITY_TYPE, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->attributeMetadataConverter->createMetadataAttribute($attribute);
            return $attributeMetadata;
        } else {
            throw new NoSuchEntityException(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS,
                [
                    'fieldName' => 'entityType',
                    'fieldValue' => self::ENTITY_TYPE,
                    'field2Name' => 'attributeCode',
                    'field2Value' => $attributeCode,
                ]
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAllAttributesMetadata()
    {
        /** @var AbstractAttribute[] $attribute */
        $attributeCodes = $this->attributeMetadataDataProvider->getAllAttributeCodes(
            self::ENTITY_TYPE,
            self::ATTRIBUTE_SET_ID
        );

        $attributesMetadata = [];

        foreach ($attributeCodes as $attributeCode) {
            try {
                $attributesMetadata[] = $this->getAttributeMetadata($attributeCode);
            } catch (NoSuchEntityException $e) {
                //If no such entity, skip
            }
        }

        return $attributesMetadata;
    }

    /**
     *  Get custom attribute metadata for the given Data object's attribute set
     *
     * @param string|null $dataObjectClassName Data object class name
     * @return \Magento\Framework\Service\Data\MetadataObjectInterface[]
     */
    public function getCustomAttributesMetadata($dataObjectClassName = self::DATA_OBJECT_CLASS_NAME)
    {
        $customAttributes = [];
        if (!$this->dataObjectMethods) {
            $this->dataObjectMethods = array_flip(get_class_methods($dataObjectClassName));
        }
        foreach ($this->getAllAttributesMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            $camelCaseKey = \Magento\Framework\Service\SimpleDataObjectConverter::snakeCaseToCamelCase($attributeCode);
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
