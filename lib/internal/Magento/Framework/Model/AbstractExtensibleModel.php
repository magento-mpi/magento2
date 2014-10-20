<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Model;

use Magento\Framework\Service\Data\MetadataServiceInterface;

/**
 * Abstract model with custom attributes support.
 *
 * This class defines basic data structure of how custom attributes are stored in an ExtensibleModel.
 * Implementations may choose to process custom attributes as their persistence requires them to.
 */
abstract class AbstractExtensibleModel extends AbstractModel implements \Magento\Framework\Api\ExtensibleDataInterface
{
    const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';

    /**
     * @var MetadataServiceInterface
     */
    protected $metadataService;

    /**
     * @var string[]
     */
    protected $customAttributesCodes = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param MetadataServiceInterface $metadataService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        MetadataServiceInterface $metadataService,
        array $data = array()
    ) {
        $this->metadataService = $metadataService;
        $data = $this->filterCustomAttributes($data);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Verify custom attributes set on $data and unset if not a valid custom attribute
     *
     * @param array $data
     * @return array processed data
     */
    protected function filterCustomAttributes($data)
    {
        if (empty($data[self::CUSTOM_ATTRIBUTES_KEY])) {
            return $data;
        }
        $customAttributesCodes = $this->getCustomAttributesCodes();
        $data[self::CUSTOM_ATTRIBUTES_KEY] =
            array_intersect_key($data[self::CUSTOM_ATTRIBUTES_KEY], $customAttributesCodes);
        return $data;
    }

    /**
     * Retrieve custom attributes values.
     *
     * @return \Magento\Framework\Service\Data\AttributeValue[]|null
     */
    public function getCustomAttributes()
    {
        // Returning as a sequential array (instead of stored associative array) to be compatible with the interface
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            ? array_values($this->_data[self::CUSTOM_ATTRIBUTES_KEY])
            : [];
    }

    /**
     * Get an attribute value.
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Service\Data\AttributeValue|null null if the attribute has not been set
     */
    public function getCustomAttribute($attributeCode)
    {
        return isset($this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode])
            ? $this->_data[self::CUSTOM_ATTRIBUTES_KEY][$attributeCode]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        if ($key == self::CUSTOM_ATTRIBUTES_KEY) {
            throw new \LogicException("Custom attributes must be set only using setCustomAttribute() method.");
        }
        return parent::setData($key, $value);
    }

    /**
     * {@inheritdoc}
     *
     * In addition to parent implementation custom attributes support is added.
     */
    public function getData($key = '', $index = null)
    {
        if ($key == self::CUSTOM_ATTRIBUTES_KEY) {
            throw new \LogicException("Custom attributes array should be retrieved via getCustomAttributes() only.");
        } else if ($key == '') {
            /** Represent model data and custom attributes as a flat array */
            $data = array_merge($this->_data, $this->getCustomAttributes());
            unset($data[self::CUSTOM_ATTRIBUTES_KEY]);
        } else {
            $data = parent::getData($key, $index);
            if ($data === null) {
                /** Try to find necessary data in custom attributes */
                $data = parent::getData(self::CUSTOM_ATTRIBUTES_KEY . "/{$key}", $index);
            }
        }
        return $data;
    }

    /**
     * Fetch all custom attributes for the given extensible model
     *
     * @return string[]
     */
    protected function getCustomAttributesCodes()
    {
        if (!is_null($this->customAttributesCodes)) {
            return $this->customAttributesCodes;
        }
        $attributeCodes = [];
        $customAttributesMetadata = $this->metadataService->getCustomAttributesMetadata(get_class($this));
        if (is_array($customAttributesMetadata)) {
            /** @var $attribute \Magento\Framework\Service\Data\MetadataObjectInterface */
            foreach ($customAttributesMetadata as $attribute) {
                // Create a map for easier processing
                $attributeCodes[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
            }
        }
        $this->customAttributesCodes = $attributeCodes;
        return $attributeCodes;
    }
}
