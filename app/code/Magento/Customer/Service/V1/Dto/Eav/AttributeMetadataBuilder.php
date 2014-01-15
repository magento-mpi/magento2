<?php
/**
 * Eav Attribute Metadata
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto\Eav;

class AttributeMetadataBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Initializes builder.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_data[AttributeMetadata::OPTIONS] = array();
    }

    /**
     * @param $attributeCode
     * @return AttributeMetadataBuilder
     */
    public function setAttributeCode($attributeCode)
    {
        return $this->_set(AttributeMetadata::ATTRIBUTE_CODE, $attributeCode);
    }

    /**
     * @param $frontendInput
     * @return AttributeMetadataBuilder
     */
    public function setFrontendInput($frontendInput)
    {
        return $this->_set(AttributeMetadata::FRONT_END_INPUT, $frontendInput);
    }

    /**
     * @param $inputFilter
     * @return AttributeMetadataBuilder
     */
    public function setInputFilter($inputFilter)
    {
        return $this->_set(AttributeMetadata::INPUT_FILTER, $inputFilter);
    }

    /**
     * @param $storeLabel
     * @return AttributeMetadataBuilder
     */
    public function setStoreLabel($storeLabel)
    {
        return $this->_set(AttributeMetadata::STORE_LABEL, $storeLabel);
    }

    /**
     * @param string $validationRules
     * @return AttributeMetadataBuilder
     */
    public function setValidationRules($validationRules)
    {
        return $this->_set(AttributeMetadata::VALIDATION_RULES, $validationRules);
    }

    /**
     * @param \Magento\Customer\Service\V1\Dto\Eav\Option[] $options
     * @return AttributeMetadataBuilder
     */
    public function setOptions($options)
    {
        $this->_set(AttributeMetadata::OPTIONS, $options);
    }

    /**
     * @param boolean $visible
     * @return AttributeMetadataBuilder
     */
    public function setIsVisible($visible)
    {
        return $this->_set(AttributeMetadata::VISIBLE, $visible);
    }

    /**
     * @param boolean $required
     * @return AttributeMetadataBuilder
     */
    public function setIsRequired($required)
    {
        return $this->_set(AttributeMetadata::REQUIRED, $required);
    }


    /**
     * @param int $count
     * @return AttributeMetadataBuilder
     */
    public function setMultilineCount($count)
    {
        return $this->_set(AttributeMetadata::MULTILINE_COUNT, $count);
    }

    /**
     * @param string
     * @return AttributeMetadataBuilder
     */
    public function setDataModel($dataModel)
    {
        return $this->_set(AttributeMetadata::DATA_MODEL, $dataModel);
    }
}
