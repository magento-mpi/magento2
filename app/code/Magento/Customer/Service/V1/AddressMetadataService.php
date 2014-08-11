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
use Magento\Framework\Service\Config\Reader as ServiceConfigReader;

/**
 * Implementation to fetch to fetch Address related custom attributes
 */
class AddressMetadataService implements AddressMetadataServiceInterface
{
    /**
     * @var Data\Eav\AttributeMetadataBuilder
     */
    private $_attributeMetadataBuilder;

    /**
     * @var array
     */
    private $addressDataObjectMethods;

    /**
     * @var ServiceConfigReader
     */
    private $serviceConfigReader;

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
     * @param ServiceConfigReader $serviceConfigReader
     * @param AttributeMetadataConverter $attributeMetadataConverter
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        Data\Eav\AttributeMetadataBuilder $attributeMetadataBuilder,
        ServiceConfigReader $serviceConfigReader,
        AttributeMetadataConverter $attributeMetadataConverter,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    ) {
        $this->_attributeMetadataBuilder = $attributeMetadataBuilder;
        $this->serviceConfigReader = $serviceConfigReader;
        $this->attributeMetadataConverter = $attributeMetadataConverter;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * TODO: Rename to getAttributeMetadata
     * @inheritdoc
     */
    public function getAddressAttributeMetadata($attributeCode)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->attributeMetadataDataProvider->getAttribute(self::ENTITY_TYPE_ADDRESS, $attributeCode);
        if ($attribute) {
            $attributeMetadata = $this->attributeMetadataConverter->createMetadataAttribute($attribute);
            return $attributeMetadata;
        } else {
            throw new NoSuchEntityException(
                NoSuchEntityException::MESSAGE_DOUBLE_FIELDS,
                [
                    'fieldName' => 'entityType',
                    'fieldValue' => self::ENTITY_TYPE_ADDRESS,
                    'field2Name' => 'attributeCode',
                    'field2Value' => $attributeCode,
                ]
            );
        }
    }

    /**
     * TODO: Rename to getAllAttributeMetadata
     * @inheritdoc
     */
    public function getAllAddressAttributeMetadata()
    {
        /** @var AbstractAttribute[] $attribute */
        $attributeCodes = $this->attributeMetadataDataProvider->getAllAttributeCodes(
            self::ENTITY_TYPE_ADDRESS,
            self::ATTRIBUTE_SET_ID_ADDRESS,
            null
        );

        $attributesMetadata = [];

        foreach ($attributeCodes as $attributeCode) {
            try {
                $attributesMetadata[] = $this->getAddressAttributeMetadata(self::ENTITY_TYPE_ADDRESS, $attributeCode);
            } catch (NoSuchEntityException $e) {
                //If no such entity, skip
            }
        }

        return $attributesMetadata;
    }

    /**
     * TODO : Rename to getCustomAttributesMetadata
     * @inheritdoc
     */
    public function getCustomAddressAttributeMetadata()
    {
        $customAttributes = [];
        $dataObjectClass = 'Magento\Customer\Service\V1\Data\Address';
        if (!$this->addressDataObjectMethods) {
            $this->addressDataObjectMethods = array_flip(get_class_methods($dataObjectClass));
        }
        foreach ($this->getAllAddressAttributeMetadata() as $attributeMetadata) {
            $attributeCode = $attributeMetadata->getAttributeCode();
            $camelCaseKey = \Magento\Framework\Service\DataObjectConverter::snakeCaseToCamelCase($attributeCode);
            $isDataObjectMethod = isset($this->addressDataObjectMethods['get' . $camelCaseKey])
                || isset($this->addressDataObjectMethods['is' . $camelCaseKey]);

            if (!$isDataObjectMethod && !$attributeMetadata->isSystem()) {
                $customAttributes[] = $attributeMetadata;
            }
        }
        $customAttributesFromConfig = $this->getAttributesFromConfig($dataObjectClass);
        return array_merge($customAttributes, $customAttributesFromConfig);
    }

    /**
     * @inheritdoc
     */
    public function getCustomAttributesMetadata()
    {
        return $this->getCustomAddressAttributeMetadata();
    }


    /**
     * Retrieve attributes defined in a config for the specified data object class.
     *
     * @param string $dataObjectClass
     * @return \Magento\Customer\Service\V1\Data\Eav\AttributeMetadata[]
     */
    protected function getAttributesFromConfig($dataObjectClass)
    {
        $attributes = [];
        $allAttributes = $this->serviceConfigReader->read();
        if (isset($allAttributes[$dataObjectClass]) && is_array($allAttributes[$dataObjectClass])) {
            foreach ($allAttributes[$dataObjectClass] as $attributeCode => $dataModel) {
                $this->_attributeMetadataBuilder
                    ->setAttributeCode($attributeCode)
                    ->setDataModel($dataModel)
                    ->setFrontendInput('')
                    ->setInputFilter('')
                    ->setStoreLabel('')
                    ->setValidationRules([])
                    ->setVisible(true)
                    ->setRequired(false)
                    ->setMultilineCount(0)
                    ->setOptions([])
                    ->setFrontendClass('')
                    ->setFrontendLabel('')
                    ->setNote('')
                    ->setIsSystem(true)
                    ->setIsUserDefined(false)
                    ->setSortOrder(0);

                $attributes[$attributeCode] = $this->_attributeMetadataBuilder->create();
            }
        }
        return $attributes;
    }
}
