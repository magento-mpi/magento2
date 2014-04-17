<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Service\Data\Eav;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class AbstractObjectBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @var AttributeValueBuilder
     */
    protected $_valueBuilder;

    /**
     * @param AttributeValueBuilder $valueBuilder
     */
    public function __construct(AttributeValueBuilder $valueBuilder)
    {
        $this->_valueBuilder = $valueBuilder;
        parent::__construct();
    }

    /**
     * Set array of custom attributes
     *
     * @param \Magento\Service\Data\Eav\AttributeValue[] $attributes
     * @return $this
     */
    public function setCustomAttributes(array $attributes)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        foreach ($attributes as $attribute) {
            if (in_array($attribute->getAttributeCode(), $customAttributesCodes)) {
                $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][] = $attribute;
            }
        }
        return $this;
    }

    /**
     * Set custom attribute value
     *
     * @param string $attributeCode
     * @param string|int|float|bool $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $customAttributesCodes = $this->getCustomAttributesCodes();
        /* If key corresponds to custom attribute code, populate custom attributes */
        if (in_array($attributeCode, $customAttributesCodes)) {
            $valueObject = $this->_valueBuilder
                ->setAttributeCode($attributeCode)
                ->setValue($attributeValue)
                ->create();
            $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][] = $valueObject;
        }
        return $this;
    }

    /**
     * Template method used to configure the attribute codes for the custom attributes
     *
     * @return string[]
     */
    public function getCustomAttributesCodes()
    {
        return array();
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
            $camelCaseKey = $this->_snakeCaseToCamelCase($key);
            $possibleMethods = array(
                'get' . $camelCaseKey,
                'is' . $camelCaseKey
            );
            if ($key == AbstractObject::CUSTOM_ATTRIBUTES_KEY && !empty($data[$key])) {
                foreach ($data[$key] as $customAttribute) {
                    $this->setCustomAttribute(
                        $customAttribute[AttributeValue::ATTRIBUTE_CODE],
                        $customAttribute[AttributeValue::VALUE]
                    );
                }
            } elseif (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->_data[$key] = $value;
            } else {
                $this->setCustomAttribute($key, $value);
            }
        }
        return $this;
    }
}
