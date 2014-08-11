<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\Eav\AttributeMetadataConverter;
use Magento\Customer\Service\V1\Data\Eav\AttributeMetadataDataProvider;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Service\Config\MetadataConfig;

/**
 * EAV attribute metadata service
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerMetadataService implements CustomerMetadataServiceInterface
{
    /**
     * @var Data\Eav\AttributeMetadataBuilder
     */
    private $_attributeMetadataBuilder;

    /**
     * @var array
     */
    private $customerDataObjectMethods;

    /**
     * @var MetadataConfig
     */
    private $metadataConfig;

    /**
     * @var AttributeMetadataConverter
     */
    private $attributeMetadataConverter;

    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @param Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder
     * @param MetadataConfig $metadataConfig
     * @param AttributeMetadataConverter $attributeMetadataConverter
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder,
        MetadataConfig $metadataConfig,
        AttributeMetadataConverter $attributeMetadataConverter,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
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
            self::ENTITY_TYPE_CUSTOMER,
            $formCode
        );
        foreach ($attributesFormCollection as $attribute) {
            $attributes[$attribute->getAttributeCode()] = $this->attributeMetadataConverter
                ->createMetadataAttribute($attribute);
        }
        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttributeMetadata($attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->attributeMetadataDataProvider->getAttribute(self::ENTITY_TYPE_CUSTOMER, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->attributeMetadataConverter->createMetadataAttribute($attribute);
            return $attributeMetadata;
        } else {
            throw new NoSuchEntityException(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS,
                [
                    'fieldName' => 'entityType',
                    'fieldValue' => self::ENTITY_TYPE_CUSTOMER,
                    'field2Name' => 'attributeCode',
                    'field2Value' => $attributeCode,
                ]
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getAllAttributeMetadata()
    {
        /** @var AbstractAttribute[] $attribute */
        $attributeCodes = $this->attributeMetadataDataProvider->getAllAttributeCodes(
            self::ENTITY_TYPE_CUSTOMER,
            self::ATTRIBUTE_SET_ID_CUSTOMER
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
     * @inheritdoc
     */
    public function getCustomAttributesMetadata()
    {
        $customAttributes = [];
        $dataObjectClass = 'Magento\Customer\Service\V1\Data\Customer';
        if (!$this->customerDataObjectMethods) {
            $this->customerDataObjectMethods = array_flip(get_class_methods($dataObjectClass));
        }
        foreach ($this->getAllAttributeMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            $camelCaseKey = \Magento\Framework\Service\DataObjectConverter::snakeCaseToCamelCase($attributeCode);
            $isDataObjectMethod = isset($this->customerDataObjectMethods['get' . $camelCaseKey])
                || isset($this->customerDataObjectMethods['is' . $camelCaseKey]);

            /** Even though disable_auto_group_change is system attribute, it should be available to the clients */
            if (!$isDataObjectMethod
                && (!$attributeMetadata->isSystem() || $attributeCode == 'disable_auto_group_change')
            ) {
                $customAttributes[] = $attributeMetadata;
            }
        }
        // TODO: Attributes from config will be read for customer instead of address until implementation of
        // TODO: MAGETWO-27167 in scope of which current metho will be moved to AddressMetadataService
        return array_merge($customAttributes, $this->metadataConfig->getCustomAttributesMetadata());
    }
}
