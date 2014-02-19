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
        return $this->_set(AttributeMetadata::OPTIONS, $options);
    }

    /**
     * @param boolean $visible
     * @return AttributeMetadataBuilder
     */
    public function setVisible($visible)
    {
        return $this->_set(AttributeMetadata::VISIBLE, $visible);
    }

    /**
     * @param boolean $required
     * @return AttributeMetadataBuilder
     */
    public function setRequired($required)
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
     * @param string $dataModel
     * @return AttributeMetadataBuilder
     */
    public function setDataModel($dataModel)
    {
        return $this->_set(AttributeMetadata::DATA_MODEL, $dataModel);
    }

    /**
     * @param $frontendClass
     * @return AttributeMetadataBuilder
     */
    public function setFrontendClass($frontendClass)
    {
        return $this->_set(AttributeMetadata::FRONTEND_CLASS, $frontendClass);
    }

    /**
     * @param $frontendInputType
     * @return AttributeMetadataBuilder
     */
    public function setFrontendInputType($frontendInputType)
    {
        return $this->_set(AttributeMetadata::FRONTEND_INPUT_TYPE, $frontendInputType);
    }

    /**
     * @param $frontendInputRendererClass
     * @return AttributeMetadataBuilder
     */
    public function setFrontendInputRendererClass($frontendInputRendererClass)
    {
        return $this->_set(AttributeMetadata::FRONTEND_INPUT_RENDERER_CLASS, $frontendInputRendererClass);
    }

    /**
     * @param bool $isUserDefined
     * @return AttributeMetadataBuilder
     */
    public function setIsUserDefined($isUserDefined)
    {
        return $this->_set(AttributeMetadata::IS_USER_DEFINED, $isUserDefined);
    }

    /**
     * @param int $sortOrder
     * @return AttributeMetadataBuilder
     */
    public function setSortOrder($sortOrder)
    {
        return $this->_set(AttributeMetadata::SORT_ORDER, $sortOrder);
    }

    /**
     * @param string $frontendLabel
     * @return AttributeMetadataBuilder
     */
    public function setFrontendLabel($frontendLabel)
    {
        return $this->_set(AttributeMetadata::FRONTEND_LABEL, $frontendLabel);
    }

    /**
     * @param bool $isSystem
     * @return AttributeMetadataBuilder
     */
    public function setIsSystem($isSystem)
    {
        return $this->_set(AttributeMetadata::IS_SYSTEM, $isSystem);
    }

    /**
     * @param string $note
     * @return AttributeMetadataBuilder
     */
    public function setNote($note)
    {
        return $this->_set(AttributeMetadata::NOTE, $note);
    }


}
