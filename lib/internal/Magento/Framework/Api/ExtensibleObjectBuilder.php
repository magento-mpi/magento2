<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

/**
 * Base Builder Class for extensible data Objects
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class ExtensibleObjectBuilder extends AbstractSimpleObjectBuilder implements ExtensibleDataBuilderInterface
{
    /**
     * @var AttributeValueBuilder
     */
    protected $attributeValueBuilder;

    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * @var string[]
     */
    protected $customAttributesCodes = null;

    /**
     * @var string
     */
    protected $modelClassInterface;

    /**
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param string|null $modelClassInterface
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        $modelClassInterface = null
    ) {
        $this->attributeValueBuilder = $valueBuilder;
        $this->metadataService = $metadataService;
        $this->modelClassInterface = $modelClassInterface;
        parent::__construct($objectFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        foreach ($attributes as $attribute) {
            if (!$attribute instanceof AttributeValue) {
                throw new \LogicException('Custom Attribute array elements can only be type of AttributeValue');
            }
            $attributeCode = $attribute->getAttributeCode();
            if (in_array($attributeCode, $customAttributesCodes)) {
                $this->data[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attribute;
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(\Magento\Framework\Api\AttributeInterface $attribute)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        /* If key corresponds to custom attribute code, populate custom attributes */
        if (in_array($attribute->getAttributeCode(), $customAttributesCodes)) {
            $this->data[AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY][$attribute->getAttributeCode()] = $attribute;
        }
        return $this;
    }

    /**
     * Template method used to configure the attribute codes for the custom attributes
     *
     * @return string[]
     */
    protected function getCustomAttributesCodes()
    {
        if (!is_null($this->customAttributesCodes)) {
            return $this->customAttributesCodes;
        }
        $attributeCodes = [];
        $customAttributesMetadata = $this->metadataService->getCustomAttributesMetadata($this->_getDataObjectType());
        if (is_array($customAttributesMetadata)) {
            foreach ($customAttributesMetadata as $attribute) {
                $attributeCodes[] = $attribute->getAttributeCode();
            }
        }
        $this->customAttributesCodes = $attributeCodes;
        return $attributeCodes;
    }

    /**
     * Set data item value.
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @deprecated This method should not be used in the client code and will be removed after Service Layer refactoring
     */
    public function set($key, $value)
    {
        return $this->_set($key, $value);
    }

    /**
     * Initializes Data Object with the data from array
     *
     * @param array $data
     * @return $this
     */
    protected function _setDataValues(array $data)
    {
        $dataObjectMethods = get_class_methods($this->_getDataObjectType());
        foreach ($data as $key => $value) {
            /* First, verify is there any getter for the key on the Service Data Object */
            $camelCaseKey = \Magento\Framework\Api\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key);
            $possibleMethods = array(
                'get' . $camelCaseKey,
                'is' . $camelCaseKey
            );
            if ($key == AbstractExtensibleObject::CUSTOM_ATTRIBUTES_KEY
                && is_array($data[$key])
                && !empty($data[$key])
            ) {
                foreach ($data[$key] as $customAttribute) {
                    $attribute = $this->attributeValueBuilder
                        ->setAttributeCode($customAttribute[AttributeValue::ATTRIBUTE_CODE])
                        ->setValue($customAttribute[AttributeValue::VALUE])
                        ->create();
                    $this->setCustomAttribute($attribute);
                }
            } elseif (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->data[$key] = $value;
            } else {
                $attribute = $this->attributeValueBuilder
                    ->setAttributeCode($key)
                    ->setValue($value)
                    ->create();
                $this->setCustomAttribute($attribute);
            }
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getDataObjectType()
    {
        return $this->modelClassInterface ?: parent::_getDataObjectType();
    }
}
