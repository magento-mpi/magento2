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
     * @param array $attributes
     * @return $this
     */
    public function setCustomAttributes($attributes)
    {
        foreach ($attributes as $attributeCode => $attributeValue) {
            $this->setCustomAttribute($attributeCode, $attributeValue);
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
            $this->_data[AbstractObject::CUSTOM_ATTRIBUTES_KEY][$attributeCode] = $valueObject;
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
            $possibleMethods = array(
                'get' . $this->_snakeCaseToCamelCase($key),
                'is' . $this->_snakeCaseToCamelCase($key)
            );
            if ($key == AbstractObject::CUSTOM_ATTRIBUTES_KEY) {
                $this->_setDataValues($value);
            } elseif (array_intersect($possibleMethods, $dataObjectMethods)) {
                $this->_data[$key] = $value;
            } else {
                $this->setCustomAttribute($key, $value);
            }
        }
        return $this;
    }
}
