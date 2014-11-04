<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Api\ExtensibleDataBuilderInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;

/**
 * Implementation for \Magento\Framework\Api\ExtensibleDataBuilderInterface.
 */
class ExtensibleDataBuilder implements ExtensibleDataBuilderInterface
{
    /**
     * @var string
     */
    protected $modelClassInterface;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * @var string[]
     */
    protected $customAttributesCodes = null;

    /**
     * @var \Magento\Framework\Api\AttributeDataBuilder
     */
    protected $attributeValueBuilder;

    /**
     * Initialize the builder
     *
     * @param ObjectManager $objectManager
     * @param MetadataServiceInterface $metadataService
     * @param \Magento\Framework\Api\AttributeDataBuilder $attributeValueBuilder
     * @param string $modelClassInterface
     */
    public function __construct(
        ObjectManager $objectManager,
        MetadataServiceInterface $metadataService,
        \Magento\Framework\Api\AttributeDataBuilder $attributeValueBuilder,
        $modelClassInterface
    ) {
        $this->objectManager = $objectManager;
        $this->metadataService = $metadataService;
        $this->modelClassInterface = $modelClassInterface;
        $this->attributeValueBuilder = $attributeValueBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $attribute = $this->attributeValueBuilder
            ->setAttributeCode($attributeCode)
            ->setValue($attributeValue)
            ->create();
        // Store as an associative array for easier lookup and processing
        $this->data[AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $attribute;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        /** @var \Magento\Framework\Api\AttributeInterface $attribute */
        foreach ($attributes as $attribute) {
            $this->data[AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY][$attribute->getAttributeCode()] = $attribute;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->objectManager->create(
            $this->modelClassInterface,
            ['data' => $this->data]
        );
    }

    /**
     * Populates the fields with data from the array.
     *
     * Keys for the map are snake_case attribute/field names.
     *
     * @param array $data
     * @return $this
     */
    public function populateWithArray(array $data)
    {
        $this->data = array();
        $this->_setDataValues($data);
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
        /** @var \Magento\Framework\Api\MetadataObjectInterface[] $customAttributesMetadata */
        $customAttributesMetadata = $this->metadataService
            ->getCustomAttributesMetadata($this->modelClassInterface);
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
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Initializes Data Object with the data from array
     *
     * @param array $data
     * @return $this
     */
    protected function _setDataValues(array $data)
    {
        $dataObjectMethods = get_class_methods($this->modelClassInterface);
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
                    $this->setCustomAttribute(
                        $customAttribute[AttributeValue::ATTRIBUTE_CODE],
                        $customAttribute[AttributeValue::VALUE]
                    );
                }
            } elseif (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->data[$key] = $value;
            } else {
                /* If key corresponds to custom attribute code, populate custom attributes */
                if (in_array($key, $this->getCustomAttributesCodes())) {
                    $this->setCustomAttribute($key, $value);
                }
            }
        }

        return $this;
    }
}
