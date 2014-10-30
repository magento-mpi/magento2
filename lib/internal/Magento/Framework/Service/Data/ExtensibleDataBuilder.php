<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\Data;

use Magento\Framework\Api\Data\ExtensibleDataBuilderInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\ObjectManager;

/**
 * Implementation for \Magento\Framework\Api\Data\ExtensibleDataBuilderInterface.
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
     * Initialize the builder
     *
     * @param ObjectManager $objectManager
     * @param string $modelClassInterface
     * @param MetadataServiceInterface $metadataService
     */
    public function __construct(
        ObjectManager $objectManager,
        $modelClassInterface,
        MetadataServiceInterface $metadataService
    ) {
        $this->objectManager = $objectManager;
        $this->modelClassInterface = $modelClassInterface;
        $this->metadataService = $metadataService;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttribute(\Magento\Framework\Api\Data\AttributeInterface $attribute)
    {
        // Store as an associative array for easier lookup and processing
        $this->data[AbstractExtensibleModel::CUSTOM_ATTRIBUTES_KEY][$attribute->getAttributeCode()]
            = $attribute;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->setCustomAttribute($attribute);
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
            $camelCaseKey = \Magento\Framework\Service\SimpleDataObjectConverter::snakeCaseToUpperCamelCase($key);
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
                $this->setCustomAttribute($key, $value);
            }
        }

        return $this;
    }
}
